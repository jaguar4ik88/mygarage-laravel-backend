<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManualSection;
use App\Models\DefaultManual;
use App\Models\DefaultManualTranslation;

class DefaultManualSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'weekly_checks' => [
                [
                    'uk' => [ 'title' => 'Щотижневі перевірки рідин', 'content' => 'Перевірте рівні масла, ОЖ, гальмової рідини, омивача.' ],
                    'ru' => [ 'title' => 'Еженедельные проверки жидкостей', 'content' => 'Проверьте уровни масла, ОЖ, тормозной жидкости, омывателя.' ],
                    'en' => [ 'title' => 'Weekly fluid checks', 'content' => 'Check oil, coolant, brake fluid and washer levels.' ],
                ],
                [
                    'uk' => [ 'title' => 'Осмотр шин', 'content' => 'Перевірте тиск і знос шин. Переконайтесь в відсутності пошкоджень і позашляхових предметів.' ],
                    'ru' => [ 'title' => 'Осмотр шин', 'content' => 'Проверьте давление и износ шин. Убедитесь в отсутствии повреждений и посторонних предметов.' ],
                    'en' => [ 'title' => 'Tire inspection', 'content' => 'Check tire pressure and wear. Ensure there are no damages or foreign objects.' ],
                ],
            ],
            'maintenance_schedule' => [
                [
                    'uk' => [ 'title' => 'Заміна масла', 'content' => 'Кожні 10–15 тис. км або раз на рік.' ],
                    'ru' => [ 'title' => 'Замена масла', 'content' => 'Каждые 10–15 тыс. км или раз в год.' ],
                    'en' => [ 'title' => 'Oil change', 'content' => 'Every 10–15k km or once a year.' ],
                ],
                [
                    'uk' => [ 'title' => 'Заміна воздушного фільтра', 'content' => 'Кожні 15–30 тис. км. Перевіряйте частіше при експлуатації в піщаних умовах.' ],
                    'ru' => [ 'title' => 'Замена воздушного фильтра', 'content' => 'Каждые 15 000–30 000 км. Проверяйте чаще при эксплуатации в пыльных условиях.' ],
                    'en' => [ 'title' => 'Air filter replacement', 'content' => 'Every 15–30k km. Check more frequently when operating in dusty conditions.' ],
                ],
            ],
            'fluids' => [
                [
                    'uk' => [ 'title' => 'Типи рідин', 'content' => 'Використовуйте рідини згідно мануалу авто.' ],
                    'ru' => [ 'title' => 'Типы жидкостей', 'content' => 'Используйте жидкости согласно руководству авто.' ],
                    'en' => [ 'title' => 'Fluid types', 'content' => 'Use manufacturer recommended fluids.' ],
                ],
            ],
            'emergency' => [
                [
                    'uk' => [ 'title' => 'Перегрів двигуна', 'content' => 'Зупиніться, вимкніть двигун, дочекайтесь охолодження.' ],
                    'ru' => [ 'title' => 'Перегрев двигателя', 'content' => 'Остановитесь, выключите двигатель, дождитесь охлаждения.' ],
                    'en' => [ 'title' => 'Engine overheating', 'content' => 'Stop, turn off engine, wait for cooling.' ],
                ],
            ],
        ];

        foreach ($data as $sectionKey => $items) {
            $section = ManualSection::where('key', $sectionKey)->first();
            if (!$section) {
                continue;
            }

            foreach ($items as $translations) {
                // Create base manual row (title/content kept for legacy, translations hold real text)
                $manual = DefaultManual::create([
                    'manual_section_id' => $section->id,
                    'title' => $translations['en']['title'] ?? 'Manual',
                    'content' => $translations['en']['content'] ?? '',
                    'pdf_path' => null,
                ]);

                foreach (['uk','ru','en'] as $locale) {
                    if (!isset($translations[$locale])) continue;
                    DefaultManualTranslation::updateOrCreate(
                        [
                            'default_manual_id' => $manual->id,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $translations[$locale]['title'],
                            'content' => $translations[$locale]['content'],
                        ]
                    );
                }
            }
        }
    }
}


