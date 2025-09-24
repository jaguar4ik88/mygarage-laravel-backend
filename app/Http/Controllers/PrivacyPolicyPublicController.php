<?php

namespace App\Http\Controllers;

use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;

class PrivacyPolicyPublicController extends Controller
{
    public function show(Request $request, string $locale)
    {
        $locale = strtolower($locale);

        $policies = PrivacyPolicy::query()
            ->forLanguage($locale)
            ->active()
            ->ordered()
            ->get(['section', 'title', 'content', 'sort_order']);

        if ($policies->isEmpty()) {
            abort(404);
        }

        return view('privacy-policy.show', [
            'locale' => $locale,
            'policies' => $policies,
        ]);
    }
}


