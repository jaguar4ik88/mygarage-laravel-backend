<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceHistory;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceHistoryController extends Controller
{
    public function index(Request $request)
    {
        // For public access, return all service history records
        $serviceHistory = ServiceHistory::with('vehicle')->get();

        return response()->json([
            'success' => true,
            'data' => $serviceHistory
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'mileage' => 'required|integer|min:0',
            'service_date' => 'required|date',
            'station_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if vehicle belongs to user
        $vehicle = $request->user()->vehicles()->findOrFail($request->vehicle_id);

        $serviceHistory = ServiceHistory::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service history record created successfully',
            'data' => $serviceHistory
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $serviceHistory = ServiceHistory::whereHas('vehicle', function($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $serviceHistory
        ]);
    }

    public function update(Request $request, $id)
    {
        $serviceHistory = ServiceHistory::whereHas('vehicle', function($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|required|string|max:255',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'sometimes|required|numeric|min:0',
            'mileage' => 'sometimes|required|integer|min:0',
            'service_date' => 'sometimes|required|date',
            'station_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $serviceHistory->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service history record updated successfully',
            'data' => $serviceHistory
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $serviceHistory = ServiceHistory::whereHas('vehicle', function($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($id);

        $serviceHistory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service history record deleted successfully'
        ]);
    }

    public function byVehicle(Request $request, $vehicleId)
    {
        // For public access, return service history for any vehicle
        $vehicle = Vehicle::findOrFail($vehicleId);
        $serviceHistory = $vehicle->serviceHistory()->get();

        return response()->json([
            'success' => true,
            'data' => $serviceHistory
        ]);
    }
}
