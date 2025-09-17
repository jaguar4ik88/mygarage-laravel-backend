<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReminderType;
use App\Models\ReminderTypeTranslation;

class ReminderTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reminderTypes = [
            'oil' => [
                'icon' => 'oil-barrel',
                'color' => '#FFA500',
                'translations' => [
                    'ru' => 'Замена масла',
                    'en' => 'Oil Change',
                ]
            ],
            'filters' => [
                'icon' => 'filter-alt',
                'color' => '#00FF00',
                'translations' => [
                    'ru' => 'Замена фильтров',
                    'en' => 'Filter Replacement',
                ]
            ],
            'tires' => [
                'icon' => 'tire-repair',
                'color' => '#FF0000',
                'translations' => [
                    'ru' => 'Проверка шин',
                    'en' => 'Tire Check',
                ]
            ],
            'brakes' => [
                'icon' => 'car-repair',
                'color' => '#FF0000',
                'translations' => [
                    'ru' => 'Тормозная система',
                    'en' => 'Brake System',
                ]
            ],
            'coolant' => [
                'icon' => 'water-drop',
                'color' => '#00BFFF',
                'translations' => [
                    'ru' => 'Охлаждающая жидкость',
                    'en' => 'Coolant',
                ]
            ],
            'inspection' => [
                'icon' => 'search',
                'color' => '#FFD700',
                'translations' => [
                    'ru' => 'Техосмотр',
                    'en' => 'Inspection',
                ]
            ],
            'timing_belt' => [
                'icon' => 'settings',
                'color' => '#FF4500',
                'translations' => [
                    'ru' => 'Ремень ГРМ',
                    'en' => 'Timing Belt',
                ]
            ],
            'transmission' => [
                'icon' => 'settings',
                'color' => '#8A2BE2',
                'translations' => [
                    'ru' => 'Коробка передач',
                    'en' => 'Transmission',
                ]
            ],
            'battery' => [
                'icon' => 'battery-full',
                'color' => '#32CD32',
                'translations' => [
                    'ru' => 'Аккумулятор',
                    'en' => 'Battery',
                ]
            ],
            'engine' => [
                'icon' => 'engineering',
                'color' => '#FF6347',
                'translations' => [
                    'ru' => 'Двигатель',
                    'en' => 'Engine',
                ]
            ],
            'electrical' => [
                'icon' => 'electrical-services',
                'color' => '#FFD700',
                'translations' => [
                    'ru' => 'Электрика',
                    'en' => 'Electrical',
                ]
            ],
            'suspension' => [
                'icon' => 'car-crash',
                'color' => '#9370DB',
                'translations' => [
                    'ru' => 'Подвеска',
                    'en' => 'Suspension',
                ]
            ],
            'other' => [
                'icon' => 'build',
                'color' => '#CCCCCC',
                'translations' => [
                    'ru' => 'Другое',
                    'en' => 'Other',
                ]
            ],
        ];

        foreach ($reminderTypes as $key => $data) {
            $reminderType = ReminderType::create([
                'key' => $key,
                'icon' => $data['icon'],
                'color' => $data['color'],
                'is_active' => true,
                'sort_order' => array_search($key, array_keys($reminderTypes)),
            ]);

            foreach ($data['translations'] as $locale => $title) {
                ReminderTypeTranslation::create([
                    'reminder_type_id' => $reminderType->id,
                    'locale' => $locale,
                    'title' => $title,
                ]);
            }
        }
    }
}
