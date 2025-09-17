<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DefaultManual;
use App\Models\DefaultManualTranslation;

class DefaultManualTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            1 => [
                'uk' => [
                    'title' => 'Перевірка рівнів рідин',
                    'content' => 'Перевірте рівні масла, охолоджуючої рідини, гальмової рідини та склоомивача. При необхідності долийте.'
                ],
                'ru' => [
                    'title' => 'Проверка уровней жидкостей',
                    'content' => 'Проверьте уровни масла, охлаждающей жидкости, тормозной жидкости и стеклоомывателя. При необходимости долейте.'
                ],
                'en' => [
                    'title' => 'Fluid Level Check',
                    'content' => 'Check the levels of oil, coolant, brake fluid, and windshield washer. Top up if necessary.'
                ]
            ],
            2 => [
                'uk' => [
                    'title' => 'Огляд шин',
                    'content' => 'Перевірте тиск та знос шин. Переконайтеся у відсутності пошкоджень та сторонніх предметів.'
                ],
                'ru' => [
                    'title' => 'Осмотр шин',
                    'content' => 'Проверьте давление и износ шин. Убедитесь в отсутствии повреждений и посторонних предметов.'
                ],
                'en' => [
                    'title' => 'Tire Inspection',
                    'content' => 'Check tire pressure and wear. Ensure there are no damages or foreign objects.'
                ]
            ],
            3 => [
                'uk' => [
                    'title' => 'Заміна масла',
                    'content' => 'Регулярна заміна моторного масла та масляного фільтра за рекомендаціями виробника.'
                ],
                'ru' => [
                    'title' => 'Замена масла',
                    'content' => 'Регулярная замена моторного масла и масляного фильтра согласно рекомендациям производителя.'
                ],
                'en' => [
                    'title' => 'Oil Change',
                    'content' => 'Regular replacement of engine oil and oil filter according to manufacturer recommendations.'
                ]
            ],
            4 => [
                'uk' => [
                    'title' => 'Заміна повітряного фільтра',
                    'content' => 'Заміна повітряного фільтра для забезпечення чистоти повітря, що надходить в двигун.'
                ],
                'ru' => [
                    'title' => 'Замена воздушного фильтра',
                    'content' => 'Замена воздушного фильтра для обеспечения чистоты воздуха, поступающего в двигатель.'
                ],
                'en' => [
                    'title' => 'Air Filter Replacement',
                    'content' => 'Replacement of air filter to ensure clean air supply to the engine.'
                ]
            ],
            5 => [
                'uk' => [
                    'title' => 'Типи рідин',
                    'content' => 'Різні типи рідин у автомобілі: моторне масло, охолоджуюча рідина, гальмова рідина, трансмісійна рідина.'
                ],
                'ru' => [
                    'title' => 'Типы жидкостей',
                    'content' => 'Различные типы жидкостей в автомобиле: моторное масло, охлаждающая жидкость, тормозная жидкость, трансмиссионная жидкость.'
                ],
                'en' => [
                    'title' => 'Types of Fluids',
                    'content' => 'Different types of fluids in the car: engine oil, coolant, brake fluid, transmission fluid.'
                ]
            ],
            6 => [
                'uk' => [
                    'title' => 'Дії при перегріві',
                    'content' => 'При перегріві двигуна зупиніть автомобіль, вимкніть двигун та дочекайтеся охолодження.'
                ],
                'ru' => [
                    'title' => 'Действия при перегреве',
                    'content' => 'При перегреве двигателя остановите автомобиль, выключите двигатель и дождитесь охлаждения.'
                ],
                'en' => [
                    'title' => 'Actions During Overheating',
                    'content' => 'If the engine overheats, stop the car, turn off the engine and wait for cooling.'
                ]
            ]
        ];

        foreach ($translations as $manualId => $localeTranslations) {
            foreach ($localeTranslations as $locale => $translation) {
                DefaultManualTranslation::updateOrCreate(
                    [
                        'default_manual_id' => $manualId,
                        'locale' => $locale,
                    ],
                    [
                        'title' => $translation['title'],
                        'content' => $translation['content'],
                    ]
                );
            }
        }
    }
}
