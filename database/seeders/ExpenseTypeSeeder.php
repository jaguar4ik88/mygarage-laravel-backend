<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseType;
use App\Models\TranslationGroup;
use App\Models\Translation;

class ExpenseTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'maintenance' => [
                'uk' => 'ТО',
                'ru' => 'Обслуживание',
                'en' => 'Maintenance'
            ],
            'repair' => [
                'uk' => 'Ремонт',
                'ru' => 'Ремонт',
                'en' => 'Repair'
            ],
            'inspection' => [
                'uk' => 'Огляд',
                'ru' => 'Техосмотр',
                'en' => 'Inspection'
            ],
            'fuel' => [
                'uk' => 'Паливо',
                'ru' => 'Топливо',
                'en' => 'Fuel'
            ]
        ];

        foreach ($types as $slug => $translations) {
            // Create translation group
            $group = TranslationGroup::create();
            
            // Create translations
            foreach ($translations as $locale => $title) {
                Translation::create([
                    'translation_group_id' => $group->id,
                    'locale' => $locale,
                    'title' => $title,
                ]);
            }

            // Create expense type
            ExpenseType::updateOrCreate(
                ['slug' => $slug],
                [
                    'is_active' => true,
                    'translation_group_id' => $group->id
                ]
            );
        }
    }
}


