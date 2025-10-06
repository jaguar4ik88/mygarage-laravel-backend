<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ManualSection;
use App\Models\TranslationGroup;
use App\Models\Translation;
use Illuminate\Support\Facades\DB;

class CreateMaintenanceSections extends Command
{
    protected $signature = 'create:maintenance-sections {--force : Force recreate existing sections}';

    protected $description = 'Create maintenance sections with translations for car recommendations';

    // Типы обслуживания с переводами
    private $maintenanceTypes = [
        'engine_oil' => [
            'slug' => 'engine-oil',
            'icon' => 'oil-can',
            'translations' => [
                'ru' => 'Масло двигателя',
                'uk' => 'Масло двигуна', 
                'en' => 'Engine Oil'
            ]
        ],
        'oil_filter' => [
            'slug' => 'oil-filter',
            'icon' => 'filter',
            'translations' => [
                'ru' => 'Масляный фильтр',
                'uk' => 'Масляний фільтр',
                'en' => 'Oil Filter'
            ]
        ],
        'air_filter' => [
            'slug' => 'air-filter',
            'icon' => 'wind',
            'translations' => [
                'ru' => 'Воздушный фильтр',
                'uk' => 'Повітряний фільтр',
                'en' => 'Air Filter'
            ]
        ],
        'brake_fluid' => [
            'slug' => 'brake-fluid',
            'icon' => 'brake-warning',
            'translations' => [
                'ru' => 'Тормозная жидкость',
                'uk' => 'Гальмівна рідина',
                'en' => 'Brake Fluid'
            ]
        ],
        'coolant' => [
            'slug' => 'coolant',
            'icon' => 'thermometer',
            'translations' => [
                'ru' => 'Антифриз',
                'uk' => 'Антифриз',
                'en' => 'Coolant'
            ]
        ],
        'spark_plugs' => [
            'slug' => 'spark-plugs',
            'icon' => 'lightning',
            'translations' => [
                'ru' => 'Свечи зажигания',
                'uk' => 'Свічки запалювання',
                'en' => 'Spark Plugs'
            ]
        ],
        'transmission_oil' => [
            'slug' => 'transmission-oil',
            'icon' => 'cog',
            'translations' => [
                'ru' => 'Трансмиссионное масло (МКПП)',
                'uk' => 'Трансмісійне масло (МКПП)',
                'en' => 'Transmission Oil (Manual)'
            ]
        ]
    ];

    public function handle()
    {
        $this->info('🔧 Creating maintenance sections with translations...');

        $force = $this->option('force');
        
        if ($force) {
            $this->warn('⚠️  Force mode enabled - existing sections will be recreated');
            $this->deleteExistingSections();
        }

        $created = 0;
        $skipped = 0;

        foreach ($this->maintenanceTypes as $typeKey => $typeData) {
            $this->line("📝 Processing {$typeKey}...");

            // Проверяем, существует ли уже секция
            $existingSection = ManualSection::where('slug', $typeData['slug'])->first();
            if ($existingSection && !$force) {
                $this->line("  ⏭️  Section '{$typeData['slug']}' already exists, skipping");
                $skipped++;
                continue;
            }

            try {
                DB::transaction(function () use ($typeKey, $typeData, &$created) {
                    // Создаем группу переводов
                    $translationGroup = TranslationGroup::create([]);

                    // Создаем переводы для всех языков
                    foreach ($typeData['translations'] as $locale => $title) {
                        Translation::create([
                            'translation_group_id' => $translationGroup->id,
                            'locale' => $locale,
                            'title' => $title,
                            'content' => null
                        ]);
                    }

                    // Создаем или обновляем секцию мануала
                    ManualSection::updateOrCreate(
                        ['slug' => $typeData['slug']],
                        [
                            'key' => $typeKey,
                            'icon' => $typeData['icon'],
                            'is_active' => true,
                            'sort_order' => $this->getSortOrder($typeKey),
                            'title_translation_id' => $translationGroup->id
                        ]
                    );

                    $this->line("  ✅ Created section '{$typeData['slug']}' with translations");
                });

                $created++;

            } catch (\Exception $e) {
                $this->error("  ❌ Failed to create section '{$typeData['slug']}': " . $e->getMessage());
            }
        }

        $this->info("✅ Maintenance sections creation completed!");
        $this->info("📈 Created: {$created} sections");
        $this->info("⏭️  Skipped: {$skipped} sections");
        
        // Показываем созданные секции
        $this->showCreatedSections();
    }

    private function deleteExistingSections()
    {
        $this->warn('🗑️  Deleting existing maintenance sections...');
        
        $sections = ManualSection::whereIn('slug', array_column($this->maintenanceTypes, 'slug'))->get();
        
        foreach ($sections as $section) {
            // Удаляем связанные переводы
            if ($section->title_translation_id) {
                $translationGroup = TranslationGroup::find($section->title_translation_id);
                if ($translationGroup) {
                    $translationGroup->translations()->delete();
                    $translationGroup->delete();
                }
            }
            
            $section->delete();
            $this->line("  🗑️  Deleted section '{$section->slug}'");
        }
    }

    private function getSortOrder($typeKey)
    {
        $sortOrders = [
            'engine_oil' => 1,
            'oil_filter' => 2,
            'air_filter' => 3,
            'brake_fluid' => 4,
            'coolant' => 5,
            'spark_plugs' => 6,
            'transmission_oil' => 7
        ];

        return $sortOrders[$typeKey] ?? 99;
    }

    private function showCreatedSections()
    {
        $this->info("\n📋 Created maintenance sections:");
        
        $sections = ManualSection::whereIn('slug', array_column($this->maintenanceTypes, 'slug'))
            ->with('titleGroup.translations')
            ->orderBy('sort_order')
            ->get();

        foreach ($sections as $section) {
            $this->line("  🔧 {$section->slug} (ID: {$section->id})");
            
            if ($section->titleGroup && $section->titleGroup->translations) {
                foreach ($section->titleGroup->translations as $translation) {
                    $this->line("    {$translation->locale}: {$translation->title}");
                }
            }
        }
    }
}