<?php

namespace App\Http\Controllers;

use App\Support\LegalDocuments;
use Illuminate\Http\Request;

class TermsOfServicePublicController extends Controller
{
    public function show(Request $request, string $locale)
    {
        $locale = strtolower($locale);

        if (! LegalDocuments::isSupported($locale)) {
            $locale = 'en';
        }

        $sections = LegalDocuments::termsSections($locale);

        if (empty($sections)) {
            abort(404);
        }

        return view('terms-of-service.show', [
            'locale' => $locale,
            'pageTitle' => LegalDocuments::termsTitle($locale),
            'sections' => $sections,
        ]);
    }
}
