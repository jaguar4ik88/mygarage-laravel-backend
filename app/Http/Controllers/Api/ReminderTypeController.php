<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReminderType;
use Illuminate\Http\Request;

class ReminderTypeController extends Controller
{
    public function index(Request $request)
    {
        $locale = $request->get('locale', 'ru');
        
        $reminderTypes = ReminderType::where('is_active', true)
            ->with(['translations' => function($query) use ($locale) {
                $query->where('locale', $locale);
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(function($type) use ($locale) {
                return [
                    'key' => $type->key,
                    'title' => $type->getTitleAttribute($locale),
                    'icon' => $type->icon,
                    'color' => $type->color,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $reminderTypes
        ]);
    }
}
