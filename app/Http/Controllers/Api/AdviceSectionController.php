<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdviceSection;
use Illuminate\Http\Request;

class AdviceSectionController extends Controller
{
    public function index(Request $request)
    {
        $locale = $request->get('locale', 'uk');

        $sections = AdviceSection::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function($section) use ($locale) {
                $group = $section->titleGroup()->with(['translations' => function($q) use ($locale) {
                    $q->orderByRaw("(locale = ?) desc, (locale = 'uk') desc", [$locale]);
                }])->first();

                $title = $group?->translations?->first()?->title ?? $section->slug;

                return [
                    'id' => $section->id,
                    'slug' => $section->slug,
                    'title' => $title,
                    'icon' => $section->icon,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $sections
        ]);
    }
}


