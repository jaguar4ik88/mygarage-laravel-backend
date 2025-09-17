<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use App\Models\FaqQuestion;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function categories(Request $request)
    {
        $locale = $request->get('locale', 'ru');
        
        $categories = FaqCategory::where('is_active', true)
            ->with(['translations' => function($query) use ($locale) {
                $query->where('locale', $locale);
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(function($category) use ($locale) {
                return [
                    'id' => $category->id,
                    'key' => $category->key,
                    'name' => $category->getNameAttribute($locale),
                    'icon' => $category->icon,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function questions(Request $request)
    {
        $locale = $request->get('locale', 'ru');
        $categoryId = $request->get('category_id');
        $search = $request->get('search');
        
        $query = FaqQuestion::where('is_active', true)
            ->with(['translations' => function($query) use ($locale) {
                $query->where('locale', $locale);
            }]);

        if ($categoryId) {
            $query->where('faq_category_id', $categoryId);
        }

        if ($search) {
            $query->whereHas('translations', function($q) use ($search, $locale) {
                $q->where('locale', $locale)
                  ->where(function($subQuery) use ($search) {
                      $subQuery->where('question', 'like', '%' . $search . '%')
                               ->orWhere('answer', 'like', '%' . $search . '%');
                  });
            });
        }

        $questions = $query->orderBy('sort_order')
            ->get()
            ->map(function($question) use ($locale) {
                $translation = $question->translations()->where('locale', $locale)->first();
                return [
                    'id' => $question->id,
                    'category_id' => $question->faq_category_id,
                    'question' => $translation ? $translation->question : '',
                    'answer' => $translation ? $translation->answer : '',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $questions
        ]);
    }

    public function index(Request $request)
    {
        $locale = $request->get('locale', 'ru');
        
        $categories = FaqCategory::where('is_active', true)
            ->with([
                'translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                },
                'questions' => function($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                },
                'questions.translations' => function($query) use ($locale) {
                    $query->where('locale', $locale);
                }
            ])
            ->orderBy('sort_order')
            ->get()
            ->map(function($category) use ($locale) {
                return [
                    'id' => $category->id,
                    'key' => $category->key,
                    'name' => $category->getNameAttribute($locale),
                    'icon' => $category->icon,
                    'questions' => $category->questions->map(function($question) use ($locale) {
                        $translation = $question->translations()->where('locale', $locale)->first();
                        return [
                            'id' => $question->id,
                            'question' => $translation ? $translation->question : '',
                            'answer' => $translation ? $translation->answer : '',
                        ];
                    })
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
