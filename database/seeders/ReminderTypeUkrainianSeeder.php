<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReminderType;
use App\Models\ReminderTypeTranslation;

class ReminderTypeUkrainianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            'oil' => 'Заміна масла',
            'filter' => 'Заміна фільтрів',
            'brakes' => 'Гальма',
            'tires' => 'Шини',
            'inspection' => 'Техогляд',
            'insurance' => 'Страхування',
            'battery' => 'Акумулятор',
            'coolant' => 'Охолоджуюча рідина',
            'transmission' => 'Трансмісія',
            'spark_plugs' => 'Свічки запалювання',
        ];

        foreach ($translations as $key => $ukrainianTitle) {
            $reminderType = ReminderType::where('key', $key)->first();
            if ($reminderType) {
                ReminderTypeTranslation::updateOrCreate(
                    [
                        'reminder_type_id' => $reminderType->id,
                        'locale' => 'uk',
                    ],
                    ['title' => $ukrainianTitle]
                );
            }
        }
    }
}
