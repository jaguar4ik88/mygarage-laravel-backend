<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CarMaker;
use App\Models\CarModel;
use App\Models\CarRecommendation;
use App\Models\ManualSection;
use Illuminate\Support\Facades\DB;
use OpenAI\Factory;

class GenerateCarRecommendations extends Command
{
    protected $signature = 'generate:car-recommendations 
                            {--limit=10 : Limit number of car combinations to process}
                            {--years=2000,2025 : Years range (comma-separated)}
                            {--force : Force regenerate existing recommendations}
                            {--openai-key= : OpenAI API key (or set OPENAI_API_KEY env var)}
                            {--periods : Use year periods instead of individual years}';

    protected $description = 'Generate car maintenance recommendations using OpenAI API for car maker/model combinations';

    // Ключи типов обслуживания (соответствуют key в manual_sections)
    private $maintenanceTypeKeys = [
        'engine_oil',
        'oil_filter', 
        'air_filter',
        'brake_fluid',
        'coolant',
        'spark_plugs',
        'transmission_oil'
    ];

    // Названия типов обслуживания для промпта
    private $maintenanceTypeNames = [
        'engine_oil' => 'Масло двигателя',
        'oil_filter' => 'Масляный фильтр',
        'air_filter' => 'Воздушный фильтр',
        'brake_fluid' => 'Тормозная жидкость',
        'coolant' => 'Антифриз',
        'spark_plugs' => 'Свечи зажигания',
        'transmission_oil' => 'Трансмиссионное масло (МКПП)'
    ];

    public function handle()
    {
        $this->info('🚗 Starting AI-powered car recommendations generation...');

        $limit = (int) $this->option('limit');
        $yearsRange = explode(',', $this->option('years'));
        $startYear = (int) $yearsRange[0];
        $endYear = (int) $yearsRange[1];
        $force = $this->option('force');
        $usePeriods = $this->option('periods');

        // Проверяем API ключ OpenAI
        $apiKey = $this->option('openai-key') ?: env('OPENAI_API_KEY');
        if (!$apiKey) {
            $this->error('❌ OpenAI API key is required. Set OPENAI_API_KEY environment variable or use --openai-key option.');
            return 1;
        }

        // Получаем ВСЕ марки из базы данных
        $makers = CarMaker::orderBy('name')->get();
        $this->info("📊 Found {$makers->count()} car makers (ALL brands)");

        // Получаем секции мануалов
        $manualSections = ManualSection::whereIn('key', $this->maintenanceTypeKeys)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('key');
        
        if ($manualSections->isEmpty()) {
            $this->error('❌ No manual sections found. Run create:maintenance-sections command first.');
            return 1;
        }

        $totalGenerated = 0;
        $processed = 0;

        foreach ($makers as $maker) {
            $this->info("🔧 Processing {$maker->name}...");
            
            // Получаем модели для этой марки
            $models = CarModel::where('car_maker_id', $maker->id)
                ->orderBy('name')
                ->limit(5) // Ограничиваем количество моделей для тестирования
                ->get();

            foreach ($models as $model) {
                if ($processed >= $limit) {
                    $this->info("⏹️  Reached limit of {$limit} combinations");
                    break 2;
                }

                $this->line("  📝 Processing {$model->name}...");
                
                if ($usePeriods) {
                    // Генерируем рекомендации для периода лет
                    $yearRange = "{$startYear}-{$endYear}";
                    $this->line("    📅 Generating for period: {$yearRange}");
                    
                    try {
                        $recommendations = $this->generateRecommendationsWithOpenAI(
                            $maker->name, 
                            $model->name, 
                            $yearRange, 
                            $manualSections,
                            $apiKey
                        );
                        
                        $created = $this->processRecommendations($recommendations, $maker->name, $model->name, $yearRange, $manualSections, $force);
                        $totalGenerated += $created;
                        
                    } catch (\Exception $e) {
                        $this->error("  ❌ Error processing {$maker->name} {$model->name} {$yearRange}: " . $e->getMessage());
                    }
                } else {
                    // Старая логика - для каждого года отдельно
                    for ($year = $startYear; $year <= $endYear; $year += 2) { // Каждые 2 года
                        try {
                            // Генерируем рекомендации через OpenAI для всех типов обслуживания
                            $recommendations = $this->generateRecommendationsWithOpenAI(
                                $maker->name, 
                                $model->name, 
                                $year, 
                                $manualSections,
                                $apiKey
                            );

                            $created = $this->processRecommendations($recommendations, $maker->name, $model->name, $year, $manualSections, $force);
                            $totalGenerated += $created;

                            // Небольшая пауза между запросами к OpenAI
                            sleep(1);

                        } catch (\Exception $e) {
                            $this->error("  ❌ Error processing {$maker->name} {$model->name} {$year}: " . $e->getMessage());
                            continue;
                        }
                    }
                }
                $processed++;
            }
        }

        $this->info("✅ AI generation completed!");
        $this->info("📈 Generated {$totalGenerated} recommendations");
        $this->info("🔧 Processed {$processed} car model combinations");
    }

    /**
     * Генерирует рекомендации через OpenAI API
     */
    private function generateRecommendationsWithOpenAI($maker, $model, $year, $manualSections, $apiKey)
    {
        $maintenanceTypes = [];
        foreach ($this->maintenanceTypeKeys as $typeKey) {
            if ($manualSections->has($typeKey)) {
                $maintenanceTypes[] = $this->maintenanceTypeNames[$typeKey];
            }
        }

        $prompt = $this->buildPrompt($maker, $model, $year, $maintenanceTypes);

        try {
            $client = (new Factory())->withApiKey($apiKey)->make();
            $response = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Ты эксперт по автомобильному обслуживанию. Отвечай только на русском языке в формате JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1500,
            ]);

            $content = $response->choices[0]->message->content;
            $this->line("  🤖 OpenAI Response: " . substr($content, 0, 100) . "...");

            // Парсим JSON ответ
            $recommendations = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from OpenAI: ' . json_last_error_msg());
            }

            return $this->parseRecommendations($recommendations);

        } catch (\Exception $e) {
            $this->error("  ❌ OpenAI API error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Строит промпт для OpenAI
     */
    private function buildPrompt($maker, $model, $year, $maintenanceTypes)
    {
        $typesList = implode(', ', $maintenanceTypes);
        
        $yearDescription = is_string($year) && strpos($year, '-') !== false 
            ? "{$year} годов" 
            : "{$year} года";
            
        return "Автомобиль: {$maker} {$model}.

Нужны рекомендации по обслуживанию для следующих типов: {$typesList}.

ВАЖНО: Раздели рекомендации по периодам годов, когда в модели были значительные изменения (рестайлинг, новая платформа, новый двигатель и т.д.).

Для каждого периода предоставь:
1. Рекомендуемый интервал пробега (в км)
2. Конкретную рекомендацию на трех языках

Ответ должен быть в формате JSON:
{
  \"2015-2018\": {
    \"engine_oil\": {
      \"mileage_interval\": 15000,
      \"recommendations\": {
        \"ru\": \"Масло для первого поколения {$maker}\",
        \"en\": \"Oil for first generation {$maker}\",
        \"uk\": \"Масло для першого покоління {$maker}\"
      }
    }
  },
  \"2019-2023\": {
    \"engine_oil\": {
      \"mileage_interval\": 15000,
      \"recommendations\": {
        \"ru\": \"Масло для второго поколения {$maker}\",
        \"en\": \"Oil for second generation {$maker}\",
        \"uk\": \"Масло для другого покоління {$maker}\"
      }
    }
  }
  // ... остальные периоды и типы обслуживания
}

Рекомендации должны быть:
- Конкретными и практичными
- Учитывать особенности марки автомобиля и периода
- Минимальными по тексту (только название продукта)
- Качественно переведенными на все три языка";
    }

    /**
     * Парсит рекомендации из ответа OpenAI (новая структура с периодами)
     */
    private function parseRecommendations($recommendations)
    {
        $parsed = [];
        
        // Новый формат: рекомендации сгруппированы по периодам
        foreach ($recommendations as $period => $periodData) {
            if (is_array($periodData)) {
                foreach ($this->maintenanceTypeKeys as $typeKey) {
                    if (isset($periodData[$typeKey])) {
                        $data = $periodData[$typeKey];
                        
                        $recommendationTexts = [];
                        
                        if (isset($data['recommendations'])) {
                            // Новый формат с переводами
                            $recommendationTexts = $data['recommendations'];
                        } elseif (isset($data['recommendation'])) {
                            // Старый формат - используем русский как основной
                            $recommendationTexts = [
                                'ru' => $data['recommendation'],
                                'en' => $this->translateToEnglish($data['recommendation'], ''),
                                'uk' => $this->translateToUkrainian($data['recommendation'], '')
                            ];
                        }
                        
                        $parsed[$period][$typeKey] = [
                            'mileage_interval' => (int) ($data['mileage_interval'] ?? 10000),
                            'recommendations' => $recommendationTexts
                        ];
                    }
                }
            }
        }

        return $parsed;
    }

    /**
     * Создает переводы рекомендации для всех языков
     */
    private function createRecommendationTranslations($carRecommendation, $recommendationData, $maker)
    {
        // Используем переводы от OpenAI или создаем базовые
        $translations = $recommendationData['recommendations'] ?? [
            'ru' => 'Рекомендация недоступна',
            'en' => 'Recommendation not available',
            'uk' => 'Рекомендація недоступна'
        ];

        foreach ($translations as $locale => $translation) {
            $carRecommendation->setRecommendationTranslation($locale, $translation);
        }
    }

    /**
     * Простой перевод на английский (базовый)
     */
    private function translateToEnglish($recommendation, $maker)
    {
        // Простые замены для базового перевода
        $replacements = [
            'синтетическое масло' => 'synthetic oil',
            'полусинтетическое масло' => 'semi-synthetic oil',
            'минеральное масло' => 'mineral oil',
            'масляный фильтр' => 'oil filter',
            'воздушный фильтр' => 'air filter',
            'тормозная жидкость' => 'brake fluid',
            'антифриз' => 'coolant',
            'свечи зажигания' => 'spark plugs',
            'трансмиссионное масло' => 'transmission oil',
            'Оригинальный' => 'Original',
            'Использовать' => 'Use',
        ];

        $translated = $recommendation;
        foreach ($replacements as $ru => $en) {
            $translated = str_ireplace($ru, $en, $translated);
        }

        // Заменяем название марки на английское
        $translated = str_ireplace($maker, $maker, $translated);

        return $translated;
    }

    /**
     * Простой перевод на украинский (базовый)
     */
    private function translateToUkrainian($recommendation, $maker)
    {
        // Простые замены для базового перевода
        $replacements = [
            'синтетическое масло' => 'синтетичне масло',
            'полусинтетическое масло' => 'напівсинтетичне масло',
            'минеральное масло' => 'мінеральне масло',
            'масляный фильтр' => 'масляний фільтр',
            'воздушный фильтр' => 'повітряний фільтр',
            'тормозная жидкость' => 'гальмівна рідина',
            'антифриз' => 'антифриз',
            'свечи зажигания' => 'свічки запалювання',
            'трансмиссионное масло' => 'трансмісійне масло',
            'Оригинальный' => 'Оригінальний',
            'Использовать' => 'Використовувати',
        ];

        $translated = $recommendation;
        foreach ($replacements as $ru => $uk) {
            $translated = str_ireplace($ru, $uk, $translated);
        }

        return $translated;
    }

    /**
     * Обрабатывает рекомендации и создает записи в базе
     */
    private function processRecommendations($recommendations, $maker, $model, $yearRange, $manualSections, $force)
    {
        $totalCreated = 0;
        
        // Новая структура: рекомендации сгруппированы по периодам
        foreach ($recommendations as $period => $periodData) {
            foreach ($periodData as $typeKey => $recommendationData) {
                $manualSection = $manualSections->get($typeKey);
                if (!$manualSection) {
                    continue;
                }

                // Проверяем, существует ли уже рекомендация
                if (!$force) {
                    $exists = CarRecommendation::where('maker', $maker)
                        ->where('model', $model)
                        ->where('year', $period)
                        ->where('manual_section_id', $manualSection->id)
                        ->exists();
                    
                    if ($exists) {
                        continue;
                    }
                }

                // Создаем рекомендацию
                $carRecommendation = CarRecommendation::create([
                    'maker' => $maker,
                    'model' => $model,
                    'year' => $period, // Используем период от OpenAI
                    'mileage_interval' => $recommendationData['mileage_interval'],
                    'manual_section_id' => $manualSection->id,
                ]);

                // Создаем переводы для всех языков
                $this->createRecommendationTranslations($carRecommendation, $recommendationData, $maker);
                $totalCreated++;
            }
        }
        
        return $totalCreated;
    }
}