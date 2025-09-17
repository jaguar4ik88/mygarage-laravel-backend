<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ManualSection;
use App\Models\ManualSectionTranslation;

class ManualSectionCompleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all manual sections from database
        $sections = ManualSection::all();
        
        foreach ($sections as $section) {
            // Create translations for each section based on its key or title
            $translations = $this->getTranslationsForSection($section->key, $section->title);
            
            foreach ($translations as $locale => $name) {
                ManualSectionTranslation::updateOrCreate(
                    [
                        'manual_section_id' => $section->id,
                        'locale' => $locale,
                    ],
                    ['title' => $name]
                );
            }
        }
    }
    
    private function getTranslationsForSection($key, $title)
    {
        // Define translations for known sections
        $knownTranslations = [
            'weekly_checks' => [
                'uk' => 'Тижневі перевірки',
                'ru' => 'Еженедельные проверки',
                'en' => 'Weekly Checks'
            ],
            'maintenance_schedule' => [
                'uk' => 'Графік обслуговування',
                'ru' => 'График обслуживания',
                'en' => 'Maintenance Schedule'
            ],
            'fluids' => [
                'uk' => 'Рідини',
                'ru' => 'Жидкости',
                'en' => 'Fluids'
            ],
            'tire_pressure' => [
                'uk' => 'Тиск у шинах',
                'ru' => 'Давление в шинах',
                'en' => 'Tire Pressure'
            ],
            'lights' => [
                'uk' => 'Освітлення',
                'ru' => 'Освещение',
                'en' => 'Lights'
            ],
            'emergency' => [
                'uk' => 'Аварійні ситуації',
                'ru' => 'Аварийные ситуации',
                'en' => 'Emergency'
            ],
            'filters' => [
                'uk' => 'Фільтри',
                'ru' => 'Фильтры',
                'en' => 'Filters'
            ],
            'tires' => [
                'uk' => 'Шини',
                'ru' => 'Шины',
                'en' => 'Tires'
            ],
            'transmission' => [
                'uk' => 'Трансмісія',
                'ru' => 'Трансмиссия',
                'en' => 'Transmission'
            ],
        ];
        
        // If we have known translations for this key, use them
        if (isset($knownTranslations[$key])) {
            return $knownTranslations[$key];
        }
        
        // For numeric keys or unknown sections, create generic translations
        $sectionNumber = is_numeric($key) ? $key : 'Unknown';
        
        return [
            'uk' => "Розділ {$sectionNumber}",
            'ru' => "Раздел {$sectionNumber}",
            'en' => "Section {$sectionNumber}"
        ];
    }
}
