<?php

use App\Models\PrivacyPolicy;
use App\Support\LegalDocuments;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Add Polish, Portuguese and Romanian privacy policy sections.
     * Existing ru/uk/en rows are left unchanged.
     */
    public function up(): void
    {
        foreach (['pl', 'pt', 'ro'] as $language) {
            foreach (LegalDocuments::privacySections($language) as $index => $section) {
                PrivacyPolicy::query()->firstOrCreate(
                    [
                        'language' => $language,
                        'section' => $section['section'],
                    ],
                    [
                        'title' => $section['title'],
                        'content' => $section['content'],
                        'is_active' => true,
                        'sort_order' => $index + 1,
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        PrivacyPolicy::query()
            ->whereIn('language', ['pl', 'pt', 'ro'])
            ->delete();
    }
};
