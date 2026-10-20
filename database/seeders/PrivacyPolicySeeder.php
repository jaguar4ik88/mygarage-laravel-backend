<?php

namespace Database\Seeders;

use App\Models\PrivacyPolicy;
use App\Support\LegalDocuments;
use Illuminate\Database\Seeder;

class PrivacyPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (LegalDocuments::LOCALES as $language) {
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
}
