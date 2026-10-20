<?php

namespace App\Http\Controllers;

use App\Models\PrivacyPolicy;
use App\Support\LegalDocuments;
use Illuminate\Http\Request;

class PrivacyPolicyPublicController extends Controller
{
    public function show(Request $request, string $locale)
    {
        $locale = strtolower($locale);

        if (! LegalDocuments::isSupported($locale)) {
            abort(404);
        }

        $policies = PrivacyPolicy::query()
            ->forLanguage($locale)
            ->active()
            ->ordered()
            ->get(['section', 'title', 'content', 'sort_order']);

        if ($policies->isEmpty()) {
            $policies = collect(LegalDocuments::privacySections($locale))
                ->map(fn (array $section) => (object) $section);
        }

        return view('privacy-policy.show', [
            'locale' => $locale,
            'pageTitle' => LegalDocuments::privacyTitle($locale),
            'policies' => $policies,
        ]);
    }
}
