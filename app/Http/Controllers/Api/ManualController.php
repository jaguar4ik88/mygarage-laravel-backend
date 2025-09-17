<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\VehicleManual;
use App\Models\DefaultManual;
use App\Models\ManualSection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ManualController extends Controller
{
    /**
     * Normalize content to plain UTF-8 text with real newlines.
     */
    private function normalizeContent(mixed $value): string
    {
        // If already array, join lines
        if (is_array($value)) {
            return implode("\n", array_map(fn($v) => is_string($v) ? $v : json_encode($v, JSON_UNESCAPED_UNICODE), $value));
        }

        if (is_string($value)) {
            // Try JSON decode once
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_string($decoded)) {
                    return $decoded;
                }
                if (is_array($decoded)) {
                    return implode("\n", array_map(fn($v) => is_string($v) ? $v : json_encode($v, JSON_UNESCAPED_UNICODE), $decoded));
                }
            }

            // If still has escape sequences like \n, unescape C-style sequences (not \u).
            $unescaped = stripcslashes($value);
            return $unescaped;
        }

        // Fallback stringify
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get all manuals for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $locale = $request->get('locale', 'ru');
            
            // Get user ID from token or request (may be null for public access)
            $userId = $request->user()?->id ?? $request->input('user_id');

            // Load user's manuals; allow forcing defaults via query param; if no user -> defaults
            $preferDefaults = filter_var($request->input('prefer_defaults'), FILTER_VALIDATE_BOOL) || !$userId;

            $userManuals = collect();
            if ($userId && !$preferDefaults) {
                $userManuals = VehicleManual::query()
                    ->where('user_id', $userId)
                    ->whereNotNull('title')
                    ->whereNotNull('content')
                    ->orderBy('id')
                    ->get();
            }

            if (!$preferDefaults && $userManuals->isNotEmpty()) {
                $sections = $userManuals->map(function ($manual) {
                    return [
                        'id' => $manual->id,
                        'title' => $manual->title,
                        'content' => $this->normalizeContent($manual->content),
                        'icon' => null,
                        'pdf_url' => $manual->pdf_url,
                        'created_at' => $manual->created_at,
                        'updated_at' => $manual->updated_at,
                    ];
                });
            } else {
                $sections = ManualSection::with([
                    'defaults' => function ($q) {
                        $q->with(['translations' => function ($query) {
                            // load all translations to allow locale fallback
                        }])->orderBy('id');
                    },
                    'translations' => function ($query) {
                        // load all translations to allow locale fallback
                    }
                ])
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get()
                    ->map(function ($section) use ($locale) {
                        $translation = $section->translations->firstWhere('locale', $locale)
                            ?? $section->translations->firstWhere('locale', 'uk')
                            ?? $section->translations->first();
                        $title = $translation?->title ?? $section->title;
                        
                        return [
                            'section' => [
                                'id' => $section->id,
                                'slug' => $section->slug,
                                'title' => $title,
                            ],
                            'items' => $section->defaults->map(function ($manual) use ($locale) {
                                $translation = $manual->translations->firstWhere('locale', $locale)
                                    ?? $manual->translations->firstWhere('locale', 'uk')
                                    ?? $manual->translations->first();
                                $title = $translation?->title ?? $manual->title;
                                $content = $translation?->content ?? $manual->content;
                                
                                return [
                                    'id' => $manual->id,
                                    'title' => $title,
                                    'content' => $this->normalizeContent($content),
                                    'icon' => null,
                                    'pdf_url' => $manual->pdf_url,
                                    'created_at' => $manual->created_at,
                                    'updated_at' => $manual->updated_at,
                                ];
                            }),
                        ];
                    });
            }

            return response()->json([
                'success' => true,
                'message' => 'Manuals retrieved successfully',
                'data' => [ 'sections' => $sections ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve manuals',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new manual for the user
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()?->id ?? $request->input('user_id');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'data' => null
                ], 401);
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'icon' => 'nullable|string|max:50',
                'pdf_path' => 'nullable|string|max:500',
            ]);

            $manual = VehicleManual::create([
                'user_id' => $userId,
                'vehicle_id' => null, // User manuals are not tied to specific vehicles
                'title' => $request->title,
                'content' => $request->content,
                'icon' => $request->icon,
                'pdf_path' => $request->pdf_path,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Manual created successfully',
                'data' => [
                    'id' => $manual->id,
                    'title' => $manual->title,
                    'content' => $manual->content,
                    'icon' => $manual->icon,
                    'pdf_url' => $manual->pdf_path,
                    'created_at' => $manual->created_at,
                    'updated_at' => $manual->updated_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create manual',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a manual
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $userId = $request->user()?->id ?? $request->input('user_id');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'data' => null
                ], 401);
            }

            $manual = VehicleManual::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$manual) {
                return response()->json([
                    'success' => false,
                    'message' => 'Manual not found',
                    'data' => null
                ], 404);
            }

            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'content' => 'sometimes|required|string',
                'icon' => 'nullable|string|max:50',
                'pdf_path' => 'nullable|string|max:500',
            ]);

            $manual->update($request->only(['title', 'content', 'icon', 'pdf_path']));

            return response()->json([
                'success' => true,
                'message' => 'Manual updated successfully',
                'data' => [
                    'id' => $manual->id,
                    'title' => $manual->title,
                    'content' => $manual->content,
                    'icon' => $manual->icon,
                    'pdf_url' => $manual->pdf_path,
                    'created_at' => $manual->created_at,
                    'updated_at' => $manual->updated_at,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update manual',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a manual
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $userId = $request->user()?->id ?? $request->input('user_id');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'data' => null
                ], 401);
            }

            $manual = VehicleManual::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$manual) {
                return response()->json([
                    'success' => false,
                    'message' => 'Manual not found',
                    'data' => null
                ], 404);
            }

            $manual->delete();

            return response()->json([
                'success' => true,
                'message' => 'Manual deleted successfully',
                'data' => null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete manual',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}