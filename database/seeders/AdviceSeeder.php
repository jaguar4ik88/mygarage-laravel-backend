<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TranslationGroup;
use App\Models\Translation;
use App\Models\AdviceSection;
use App\Models\AdviceItem;

class AdviceSeeder extends Seeder
{
    public function run(): void
    {
        $this->createAdviceSections();
    }

    private function createAdviceSections(): void
    {
        $sections = [
            [
                'slug' => 'weekly_checks',
                'icon' => 'calendar-week',
                'sort_order' => 1,
                'title_translations' => [
                    'en' => 'Weekly Checks',
                    'ru' => 'Еженедельные проверки',
                    'uk' => 'Щотижневі перевірки',
                ],
                'items' => [
                    [
                        'icon' => 'tint',
                        'sort_order' => 1,
                        'title_translations' => [
                            'en' => 'Check Engine Oil',
                            'ru' => 'Проверка моторного масла',
                            'uk' => 'Перевірка моторного масла',
                        ],
                        'content_translations' => [
                            'en' => "• Park on level ground\n• Wait 5 minutes after engine shutdown\n• Pull out dipstick, wipe clean\n• Reinsert fully, then check level\n• Oil should be between min/max marks\n• Check color - should be amber/black, not milky",
                            'ru' => "• Поставьте автомобиль на ровную поверхность\n• Подождите 5 минут после остановки двигателя\n• Вытащите щуп, протрите насухо\n• Вставьте обратно до упора, затем проверьте уровень\n• Масло должно быть между отметками min/max\n• Проверьте цвет - должен быть янтарным/черным, не молочным",
                            'uk' => "• Поставте автомобіль на рівну поверхню\n• Зачекайте 5 хвилин після зупинки двигуна\n• Витягніть щуп, протріть насухо\n• Вставте назад до упору, потім перевірте рівень\n• Масло має бути між позначками min/max\n• Перевірте колір - має бути бурштиновим/чорним, не молочним",
                        ],
                    ],
                    [
                        'icon' => 'tire',
                        'sort_order' => 2,
                        'title_translations' => [
                            'en' => 'Tire Pressure Check',
                            'ru' => 'Проверка давления в шинах',
                            'uk' => 'Перевірка тиску в шинах',
                        ],
                        'content_translations' => [
                            'en' => "• Check when tires are cold (not driven for 3+ hours)\n• Use accurate pressure gauge\n• Check all 4 tires plus spare\n• Compare with recommended pressure (found in door jamb or manual)\n• Inflate if 25% below recommended\n• Check for uneven wear patterns",
                            'ru' => "• Проверяйте когда шины холодные (не ездили 3+ часа)\n• Используйте точный манометр\n• Проверьте все 4 шины плюс запасную\n• Сравните с рекомендуемым давлением (найдено на стойке двери или в руководстве)\n• Накачайте если на 25% ниже рекомендуемого\n• Проверьте на неравномерный износ",
                            'uk' => "• Перевіряйте коли шини холодні (не їздили 3+ години)\n• Використовуйте точний манометр\n• Перевірте всі 4 шини плюс запасну\n• Порівняйте з рекомендованим тиском (знайдено на стійці дверей або в керівництві)\n• Накачайте якщо на 25% нижче рекомендованого\n• Перевірте на нерівномірний знос",
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'maintenance_schedule',
                'icon' => 'calendar-alt',
                'sort_order' => 2,
                'title_translations' => [
                    'en' => 'Maintenance Schedule',
                    'ru' => 'Регламент ТО',
                    'uk' => 'Регламент ТО',
                ],
                'items' => [
                    [
                        'icon' => 'oil-can',
                        'sort_order' => 1,
                        'title_translations' => [
                            'en' => 'Oil Change Intervals',
                            'ru' => 'Интервалы замены масла',
                            'uk' => 'Інтервали заміни масла',
                        ],
                        'content_translations' => [
                            'en' => "• Conventional oil: every 3,000-5,000 miles\n• Synthetic oil: every 7,500-10,000 miles\n• Check owner's manual for specific intervals\n• More frequent changes if: severe driving conditions, towing, dusty environment\n• Always change oil filter with oil\n• Reset oil change indicator after service",
                            'ru' => "• Обычное масло: каждые 3,000-5,000 миль\n• Синтетическое масло: каждые 7,500-10,000 миль\n• Проверьте руководство владельца для конкретных интервалов\n• Более частая замена если: тяжелые условия вождения, буксировка, пыльная среда\n• Всегда меняйте масляный фильтр с маслом\n• Сбросьте индикатор замены масла после обслуживания",
                            'uk' => "• Звичайне масло: кожні 3,000-5,000 миль\n• Синтетичне масло: кожні 7,500-10,000 миль\n• Перевірте керівництво власника для конкретних інтервалів\n• Більш частина заміна якщо: важкі умови водіння, буксирування, пильне середовище\n• Завжди міняйте масляний фільтр з маслом\n• Скиньте індикатор заміни масла після обслуговування",
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'fluids',
                'icon' => 'tint',
                'sort_order' => 3,
                'title_translations' => [
                    'en' => 'Fluid Maintenance',
                    'ru' => 'Обслуживание жидкостей',
                    'uk' => 'Обслуговування рідин',
                ],
                'items' => [
                    [
                        'icon' => 'thermometer',
                        'sort_order' => 1,
                        'title_translations' => [
                            'en' => 'Coolant System',
                            'ru' => 'Система охлаждения',
                            'uk' => 'Система охолодження',
                        ],
                        'content_translations' => [
                            'en' => "• Check coolant level when engine is cold\n• Look for proper color (green, orange, or pink depending on type)\n• Never open radiator cap when hot\n• Mix with distilled water if needed\n• Replace every 2-3 years or 30,000-50,000 miles\n• Check for leaks around hoses and connections",
                            'ru' => "• Проверяйте уровень охлаждающей жидкости когда двигатель холодный\n• Ищите правильный цвет (зеленый, оранжевый или розовый в зависимости от типа)\n• Никогда не открывайте крышку радиатора когда горячий\n• Смешивайте с дистиллированной водой если нужно\n• Заменяйте каждые 2-3 года или 30,000-50,000 миль\n• Проверяйте на утечки вокруг шлангов и соединений",
                            'uk' => "• Перевіряйте рівень охолоджуючої рідини коли двигун холодний\n• Шукайте правильний колір (зелений, помаранчевий або рожевий залежно від типу)\n• Ніколи не відкривайте кришку радіатора коли гарячий\n• Змішуйте з дистильованою водою якщо потрібно\n• Замінюйте кожні 2-3 роки або 30,000-50,000 миль\n• Перевіряйте на витічки навколо шлангів та з'єднань",
                        ],
                    ],
                ],
            ],
        ];

        foreach ($sections as $sectionData) {
            // Create title translation group
            $titleGroup = TranslationGroup::create();
            foreach ($sectionData['title_translations'] as $locale => $title) {
                Translation::create([
                    'translation_group_id' => $titleGroup->id,
                    'locale' => $locale,
                    'title' => $title,
                ]);
            }

            // Create advice section
            $section = AdviceSection::create([
                'slug' => $sectionData['slug'],
                'icon' => $sectionData['icon'],
                'sort_order' => $sectionData['sort_order'],
                'is_active' => true,
                'title_translation_id' => $titleGroup->id,
            ]);

            // Create advice items
            foreach ($sectionData['items'] as $itemData) {
                // Create title translation group for item
                $itemTitleGroup = TranslationGroup::create();
                foreach ($itemData['title_translations'] as $locale => $title) {
                    Translation::create([
                        'translation_group_id' => $itemTitleGroup->id,
                        'locale' => $locale,
                        'title' => $title,
                    ]);
                }

                // Create content translation group for item
                $itemContentGroup = TranslationGroup::create();
                foreach ($itemData['content_translations'] as $locale => $content) {
                    Translation::create([
                        'translation_group_id' => $itemContentGroup->id,
                        'locale' => $locale,
                        'content' => $content,
                    ]);
                }

                // Create advice item
                AdviceItem::create([
                    'advice_section_id' => $section->id,
                    'title_translation_id' => $itemTitleGroup->id,
                    'content_translation_id' => $itemContentGroup->id,
                    'icon' => $itemData['icon'],
                    'sort_order' => $itemData['sort_order'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
