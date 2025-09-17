<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdviceItem;
use App\Models\AdviceSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdviceController extends Controller
{
    private function translate(?int $groupId, string $locale, ?string $fallbackField = null, ?string $fallbackValue = null): array
    {
        if (!$groupId) {
            return [
                'title' => $fallbackField === 'title' ? $fallbackValue : null,
                'content' => $fallbackField === 'content' ? $fallbackValue : null,
            ];
        }

        $group = \App\Models\TranslationGroup::with(['translations' => function($q) use ($locale) {
            $q->orderByRaw("(locale = ?) desc, (locale = 'uk') desc", [$locale]);
        }])->find($groupId);

        $translation = $group?->translations?->first();
        return [
            'title' => $translation?->title,
            'content' => $translation?->content,
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', 'uk');

        $sections = AdviceSection::with(['items' => function($q){
            $q->where('is_active', true)->orderBy('sort_order')->orderBy('id');
        }])->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function(AdviceSection $section) use ($locale) {
                $title = $this->translate($section->title_translation_id, $locale, 'title', $section->slug)['title'] ?? $section->slug;
                return [
                    'section' => [
                        'id' => $section->id,
                        'slug' => $section->slug,
                        'title' => $title,
                    ],
                    'items' => $section->items->map(function(AdviceItem $item) use ($locale) {
                        $title = $this->translate($item->title_translation_id, $locale, 'title')['title'];
                        $content = $this->translate($item->content_translation_id, $locale, 'content')['content'];
                        return [
                            'id' => $item->id,
                            'title' => $title,
                            'content' => $content,
                            'icon' => $item->icon,
                            'pdf_url' => $item->pdf_path,
                            'created_at' => $item->created_at,
                            'updated_at' => $item->updated_at,
                        ];
                    })
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Advice retrieved successfully',
            'data' => [ 'sections' => $sections ]
        ]);
    }
}


