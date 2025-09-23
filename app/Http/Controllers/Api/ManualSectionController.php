<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ManualSection;
use Illuminate\Http\Request;

class ManualSectionController extends Controller
{
    public function index(Request $request)
    {
        $locale = $request->get('locale', 'uk');
        
        $manualSections = ManualSection::where('is_active', true)
            ->with(['translations' => function($query) use ($locale) {
                $query->where('locale', $locale);
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(function($section) use ($locale) {
                return [
                    'id' => $section->id,
                    'key' => $section->key,
                    'title' => $section->getTitleAttribute($locale),
                    'icon' => $section->icon,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $manualSections
        ]);
    }
}
