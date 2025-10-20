<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleManual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        // Get user ID from request (for now default to 1, later should come from auth)
        $userId = $request->get('user_id', 1);
        
        // Return only user's vehicles sorted by last_modified_at desc, then by added_at desc
        $vehicles = Vehicle::where('user_id', $userId)
            ->orderBy('last_modified_at', 'desc')
            ->orderBy('added_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $vehicles
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'engine_type' => 'nullable|string|max:255',
            'mileage' => 'required|integer|min:0',
            'vin' => 'nullable|string|max:17', // Убрали unique для тестирования
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get user from auth or fallback to request user_id
        $user = $request->user();
        $userId = $user ? $user->id : $request->get('user_id', 1);

        // Check subscription limits if user is authenticated
        if ($user && !$user->canAddVehicle()) {
            $maxVehicles = $user->getMaxVehicles();
            $planType = $user->plan_type;
            
            return response()->json([
                'success' => false,
                'message' => "You have reached the maximum number of vehicles ($maxVehicles) for your $planType plan",
                'upgrade_required' => true,
                'limit_reached' => true,
                'max_vehicles' => $maxVehicles,
                'current_plan' => $planType,
            ], 403);
        }

        // Create vehicle
        $vehicleData = $request->all();
        $vehicleData['user_id'] = $userId;
        $vehicleData['added_at'] = now();
        $vehicleData['last_modified_at'] = now();
        $vehicle = Vehicle::create($vehicleData);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle created successfully',
            'data' => $vehicle
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $vehicle
        ]);
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'make' => 'sometimes|required|string|max:255',
            'model' => 'sometimes|required|string|max:255',
            'year' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
            'engine_type' => 'nullable|string|max:255',
            'mileage' => 'sometimes|required|integer|min:0',
            'vin' => 'nullable|string|max:17', // Убрали unique для тестирования
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->all();
        $updateData['last_modified_at'] = now();
        $vehicle->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle updated successfully',
            'data' => $vehicle
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle deleted successfully'
        ]);
    }

    public function manual(Request $request, $id)
    {
        // For public access, find vehicle by ID
        $vehicle = Vehicle::findOrFail($id);

        // First try to get vehicle-specific manuals
        $manuals = VehicleManual::forVehicle($id)->where('is_active', true)->orderBy('sort_order')->get();

        // If no vehicle-specific manuals, get default ones
        if ($manuals->isEmpty()) {
            $manuals = VehicleManual::default()->where('is_active', true)->orderBy('sort_order')->get();
        }

        if ($manuals->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => ['sections' => []]
            ]);
        }

        $sections = $manuals->map(function ($manual) {
            return [
                'id' => $manual->section_id,
                'title' => $manual->title,
                'content' => $manual->content,
                'icon' => $manual->icon,
                'pdf_url' => $manual->pdf_url,
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'data' => ['sections' => $sections]
        ]);
    }

    public function uploadManualPdf(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'pdf' => 'required|file|mimes:pdf|max:20480', // 20MB
            'section_id' => 'nullable|string',
            'title' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('pdf');
        $path = $file->store('manuals', 'public');

        // Create or update manual record for the vehicle
        $manual = VehicleManual::firstOrCreate(
            [
                'vehicle_id' => $vehicle->id,
                'section_id' => $request->input('section_id', 'pdf_manual'),
            ],
            [
                'title' => $request->input('title', 'Руководство пользователя (PDF)'),
                'content' => [],
                'icon' => 'file-pdf',
                'sort_order' => 0,
                'is_active' => true,
            ]
        );

        $manual->pdf_path = $path;
        $manual->save();

        return response()->json([
            'success' => true,
            'message' => 'PDF успешно загружен',
            'data' => [
                'section' => [
                    'id' => $manual->section_id,
                    'title' => $manual->title,
                    'content' => $manual->content,
                    'icon' => $manual->icon,
                    'pdf_url' => $manual->pdf_url,
                ]
            ]
        ], 201);
    }
}
