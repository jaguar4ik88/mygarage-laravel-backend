<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManualSection;
use App\Models\ManualSectionTranslation;

class ManualSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            'weekly_checks' => [
                'icon' => 'calendar-week',
                'translations' => [
                    'ru' => 'Еженедельные проверки',
                    'en' => 'Weekly Checks',
                ]
            ],
            'maintenance_schedule' => [
                'icon' => 'calendar-alt',
                'translations' => [
                    'ru' => 'Регламент ТО',
                    'en' => 'Maintenance Schedule',
                ]
            ],
            'fluids' => [
                'icon' => 'tint',
                'translations' => [
                    'ru' => 'Жидкости',
                    'en' => 'Fluids',
                ]
            ],
            'tire_pressure' => [
                'icon' => 'tire',
                'translations' => [
                    'ru' => 'Давление шин',
                    'en' => 'Tire Pressure',
                ]
            ],
            'lights' => [
                'icon' => 'lightbulb',
                'translations' => [
                    'ru' => 'Освещение',
                    'en' => 'Lights',
                ]
            ],
            'emergency' => [
                'icon' => 'exclamation-triangle',
                'translations' => [
                    'ru' => 'Экстренные ситуации',
                    'en' => 'Emergency',
                ]
            ],
            'filters' => [
                'icon' => 'filter',
                'translations' => [
                    'ru' => 'Фильтры',
                    'en' => 'Filters',
                ]
            ],
            'tires' => [
                'icon' => 'tire',
                'translations' => [
                    'ru' => 'Шины',
                    'en' => 'Tires',
                ]
            ],
            'transmission' => [
                'icon' => 'cogs',
                'translations' => [
                    'ru' => 'Коробка передач',
                    'en' => 'Transmission',
                ]
            ],
        ];

        foreach ($sections as $key => $data) {
            $section = ManualSection::updateOrCreate(
                ['key' => $key],
                [
                    'icon' => $data['icon'],
                    'is_active' => true,
                    'sort_order' => array_search($key, array_keys($sections)),
                ]
            );

            foreach ($data['translations'] as $locale => $title) {
                ManualSectionTranslation::updateOrCreate(
                    [
                        'manual_section_id' => $section->id,
                        'locale' => $locale,
                    ],
                    ['title' => $title]
                );
            }
        }
    }
}


