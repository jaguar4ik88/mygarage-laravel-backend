<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;

class AdminVehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with('user');

        // Поиск
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('year', 'like', "%{$search}%");
            });
        }

        // Фильтр по пользователю
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        // Фильтр по марке
        if ($request->filled('make')) {
            $query->where('make', $request->get('make'));
        }

        $vehicles = $query
            ->orderByDesc('last_modified_at')
            ->orderByDesc('added_at')
            ->paginate(15);
        $users = User::orderBy('name')->get();
        $makes = Vehicle::distinct()->pluck('make')->sort();

        return view('admin.vehicles.index', compact('vehicles', 'users', 'makes'));
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['user', 'reminders', 'serviceHistory']);
        return view('admin.vehicles.show', compact('vehicle'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('admin.vehicles.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|integer|min:0',
            'vin' => 'nullable|string|max:17|unique:vehicles,vin',
            'license_plate' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $vehicle = Vehicle::create($request->all());

        return redirect()->route('admin.vehicles.show', $vehicle)
            ->with('success', 'Транспортное средство успешно создано');
    }

    public function edit(Vehicle $vehicle)
    {
        $users = User::orderBy('name')->get();
        return view('admin.vehicles.edit', compact('vehicle', 'users'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|integer|min:0',
            'vin' => 'nullable|string|max:17|unique:vehicles,vin,' . $vehicle->id,
            'license_plate' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $vehicle->update($request->all());

        return redirect()->route('admin.vehicles.show', $vehicle)
            ->with('success', 'Транспортное средство успешно обновлено');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Транспортное средство успешно удалено');
    }
}
