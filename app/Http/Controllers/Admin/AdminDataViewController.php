<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDataViewController extends Controller
{
    // Просмотр всех транспортных средств
    public function vehicles(Request $request)
    {
        $query = Vehicle::with('user');

        // Поиск
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('year', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%");
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
            ->paginate(20);
        $users = User::orderBy('name')->get();
        $makes = Vehicle::distinct()->pluck('make')->sort();

        return view('admin.data.vehicles', compact('vehicles', 'users', 'makes'));
    }

    // Просмотр конкретного транспортного средства
    public function vehicleShow(Vehicle $vehicle)
    {
        $vehicle->load(['user', 'reminders', 'serviceHistory']);
        return view('admin.data.vehicle-show', compact('vehicle'));
    }


    // Статистика по пользовательским данным
    public function statistics()
    {
        $stats = [
            'users' => User::count(),
            'vehicles' => Vehicle::count(),
            'reminder_types' => \App\Models\ReminderType::count(),
            'manual_sections' => \App\Models\ManualSection::count(),
            'faq_categories' => \App\Models\FaqCategory::count(),
            'faq_questions' => \App\Models\FaqQuestion::count(),
            'expenses_records' => \App\Models\ExpensesHistory::count(),
        ];

        // Топ марок автомобилей
        $topMakes = Vehicle::selectRaw('make, COUNT(*) as count')
            ->groupBy('make')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        // Последние пользователи
        $recentUsers = User::withCount('vehicles')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.data.statistics', compact('stats', 'topMakes', 'recentUsers'));
    }
}
