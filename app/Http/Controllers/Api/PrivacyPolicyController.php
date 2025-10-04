<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PrivacyPolicyController extends Controller
{
    /**
     * Get privacy policy for specific language
     */
    public function show(Request $request, string $language): JsonResponse
    {
        $language = strtolower($language);
        
        // Validate language
        if (!in_array($language, ['ru', 'uk', 'en'])) {
            $language = 'en'; // fallback to English
        }

        $sections = PrivacyPolicy::forLanguage($language)
            ->active()
            ->ordered()
            ->get()
            ->map(function ($item) {
                return [
                    'section' => $item->section,
                    'title' => $item->title,
                    'content' => $item->content,
                ];
            });

        return response()->json([
            'success' => true,
            'language' => $language,
            'sections' => $sections,
        ]);
    }

    /**
     * Get all privacy policy sections for admin
     */
    public function index(): JsonResponse
    {
        $policies = PrivacyPolicy::ordered()
            ->get()
            ->groupBy('language');

        return response()->json([
            'success' => true,
            'policies' => $policies,
        ]);
    }

    /**
     * Store new privacy policy section
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'language' => 'required|string|in:ru,uk,en',
            'section' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $policy = PrivacyPolicy::create([
            'language' => $request->language,
            'section' => $request->section,
            'title' => $request->title,
            'content' => $request->content,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'success' => true,
            'policy' => $policy,
        ], 201);
    }

    /**
     * Update privacy policy section
     */
    public function update(Request $request, PrivacyPolicy $privacyPolicy): JsonResponse
    {
        $request->validate([
            'language' => 'sometimes|string|in:ru,uk,en',
            'section' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'sort_order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $privacyPolicy->update($request->only([
            'language',
            'section', 
            'title',
            'content',
            'sort_order',
            'is_active',
        ]));

        return response()->json([
            'success' => true,
            'policy' => $privacyPolicy->fresh(),
        ]);
    }

    /**
     * Delete privacy policy section
     */
    public function destroy(PrivacyPolicy $privacyPolicy): JsonResponse
    {
        $privacyPolicy->delete();

        return response()->json([
            'success' => true,
            'message' => 'Privacy policy section deleted successfully',
        ]);
    }
}