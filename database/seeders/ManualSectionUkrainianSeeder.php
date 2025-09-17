<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ManualSection;
use App\Models\ManualSectionTranslation;

class ManualSectionUkrainianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            'maintenance' => 'Обслуговування',
            'repairs' => 'Ремонти',
            'parts' => 'Запчастини',
            'troubleshooting' => 'Діагностика',
            'specifications' => 'Характеристики',
            'warranty' => 'Гарантія',
            'safety' => 'Безпека',
            'environment' => 'Екологія',
            'emergency' => 'Аварійні ситуації',
            'filters' => 'Фільтри',
            'fluids' => 'Рідини',
            'lights' => 'Освітлення',
            'maintenance_schedule' => 'Графік обслуговування',
            'tire_pressure' => 'Тиск у шинах',
            'tires' => 'Шини',
            'transmission' => 'Трансмісія',
            'weekly_checks' => 'Тижневі перевірки',
        ];

        foreach ($translations as $key => $ukrainianTitle) {
            $manualSection = ManualSection::where('key', $key)->first();
            if ($manualSection) {
                ManualSectionTranslation::updateOrCreate(
                    [
                        'manual_section_id' => $manualSection->id,
                        'locale' => 'uk',
                    ],
                    ['title' => $ukrainianTitle]
                );
            }
        }
    }
}
