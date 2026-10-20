<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\LegalDocuments;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TermsOfServiceController extends Controller
{
    /**
     * Get terms of service for specific language
     */
    public function show(Request $request, string $language): JsonResponse
    {
        $language = strtolower($language);

        if (! LegalDocuments::isSupported($language)) {
            $language = 'en';
        }

        return response()->json([
            'success' => true,
            'language' => $language,
            'sections' => LegalDocuments::termsSections($language),
        ]);
    }
}
