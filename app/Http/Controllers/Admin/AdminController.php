<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\ServiceStation;
use App\Models\ReminderType;
use App\Models\ManualSection;
use App\Models\FaqCategory;
use App\Models\FaqQuestion;
use App\Models\ExpensesHistory;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function dashboard()
    {
        $stats = [
            'users' => User::count(),
            'vehicles' => Vehicle::count(),
            'service_stations' => ServiceStation::count(),
            'reminder_types' => ReminderType::count(),
            'manual_sections' => ManualSection::count(),
            'faq_categories' => FaqCategory::count(),
            'faq_questions' => FaqQuestion::count(),
            'expenses_records' => ExpensesHistory::count(),
        ];

        // Статистика по месяцам (упрощенная для совместимости)
        $monthlyStats = [
            'users' => collect(),
            'vehicles' => collect(),
        ];

        // Последние пользователи
        $recentUsers = User::latest()->take(5)->get();

        // Последние транспортные средства (по last_modified_at, затем added_at)
        $recentVehicles = Vehicle::with('user')
            ->orderByDesc('last_modified_at')
            ->orderByDesc('added_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'monthlyStats', 'recentUsers', 'recentVehicles'));
    }
}
