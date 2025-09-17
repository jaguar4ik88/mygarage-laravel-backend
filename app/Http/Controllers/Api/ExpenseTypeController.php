<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseTypeController extends Controller
{
    public function index(Request $request)
    {
        $locale = $request->query('locale', 'uk');
        
        $expenseTypes = ExpenseType::with('translationGroup.translations')
            ->where('is_active', true)
            ->get()
            ->map(function ($type) use ($locale) {
                return [
                    'id' => $type->id,
                    'slug' => $type->slug,
                    'name' => $type->getTranslatedName($locale),
                    'translations' => $type->translationGroup ? 
                        $type->translationGroup->translations->mapWithKeys(function ($translation) {
                            return [$translation->locale => $translation->title];
                        }) : []
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $expenseTypes
        ]);
    }
}
