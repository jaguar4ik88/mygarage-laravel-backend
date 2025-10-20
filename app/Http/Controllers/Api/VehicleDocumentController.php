<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VehicleDocumentController extends Controller
{
    /**
     * Get all documents for a vehicle
     */
    public function index(Request $request, int $vehicleId): JsonResponse
    {
        $user = $request->user();

        // Проверяем доступ к PRO функциям
        if (!$user->isPro()) {
            return response()->json([
                'success' => false,
                'message' => 'This feature requires PRO subscription',
                'upgrade_required' => true,
            ], 403);
        }

        // Проверяем, что автомобиль принадлежит пользователю
        $vehicle = Vehicle::where('id', $vehicleId)
            ->where('user_id', $user->id)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found',
            ], 404);
        }

        $documents = VehicleDocument::where('vehicle_id', $vehicleId)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $documents,
        ]);
    }

    /**
     * Upload a new document
     */
    public function store(Request $request, int $vehicleId): JsonResponse
    {
        $user = $request->user();

        // Проверяем доступ к PRO функциям
        if (!$user->isPro()) {
            return response()->json([
                'success' => false,
                'message' => 'This feature requires PRO subscription',
                'upgrade_required' => true,
            ], 403);
        }

        // Проверяем, что автомобиль принадлежит пользователю
        $vehicle = Vehicle::where('id', $vehicleId)
            ->where('user_id', $user->id)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:insurance,power_of_attorney,certificate,other',
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240', // max 10MB
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Сохраняем файл
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents/' . $user->id . '/' . $vehicleId, $fileName);

            $document = VehicleDocument::create([
                'vehicle_id' => $vehicleId,
                'user_id' => $user->id,
                'type' => $data['type'],
                'name' => $data['name'],
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'expiry_date' => $data['expiry_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'data' => $document,
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'No file provided',
        ], 400);
    }

    /**
     * Get a specific document
     */
    public function show(Request $request, int $documentId): JsonResponse
    {
        $user = $request->user();

        // Проверяем доступ к PRO функциям
        if (!$user->isPro()) {
            return response()->json([
                'success' => false,
                'message' => 'This feature requires PRO subscription',
                'upgrade_required' => true,
            ], 403);
        }

        $document = VehicleDocument::where('id', $documentId)
            ->where('user_id', $user->id)
            ->first();

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $document,
        ]);
    }

    /**
     * Update a document
     */
    public function update(Request $request, int $documentId): JsonResponse
    {
        $user = $request->user();

        // Проверяем доступ к PRO функциям
        if (!$user->isPro()) {
            return response()->json([
                'success' => false,
                'message' => 'This feature requires PRO subscription',
                'upgrade_required' => true,
            ], 403);
        }

        $document = VehicleDocument::where('id', $documentId)
            ->where('user_id', $user->id)
            ->first();

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:insurance,power_of_attorney,certificate,other',
            'name' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $document->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully',
            'data' => $document->fresh(),
        ]);
    }

    /**
     * Delete a document
     */
    public function destroy(Request $request, int $documentId): JsonResponse
    {
        $user = $request->user();

        // Проверяем доступ к PRO функциям
        if (!$user->isPro()) {
            return response()->json([
                'success' => false,
                'message' => 'This feature requires PRO subscription',
                'upgrade_required' => true,
            ], 403);
        }

        $document = VehicleDocument::where('id', $documentId)
            ->where('user_id', $user->id)
            ->first();

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found',
            ], 404);
        }

        $document->delete(); // File will be deleted automatically via model event

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully',
        ]);
    }

    /**
     * Download a document file
     */
    public function download(Request $request, int $documentId)
    {
        $user = $request->user();

        // Проверяем доступ к PRO функциям
        if (!$user->isPro()) {
            return response()->json([
                'success' => false,
                'message' => 'This feature requires PRO subscription',
                'upgrade_required' => true,
            ], 403);
        }

        $document = VehicleDocument::where('id', $documentId)
            ->where('user_id', $user->id)
            ->first();

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found',
            ], 404);
        }

        if (!Storage::exists($document->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found on server',
            ], 404);
        }

        return Storage::download($document->file_path, $document->file_name);
    }
}
