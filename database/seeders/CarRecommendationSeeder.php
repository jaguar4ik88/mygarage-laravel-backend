<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarRecommendation;
use App\Models\ManualSection;

class CarRecommendationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем секции руководств для связывания
        $maintenanceSection = ManualSection::where('key', 'maintenance')->first();
        $engineSection = ManualSection::where('key', 'engine')->first();

        $recommendations = [
            // Toyota Corolla
            [
                'maker' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2020,
                'mileage_interval' => 10000,
                'item' => 'Масло двигателя',
                'recommendation' => 'Менять масло каждые 10 000 км, использовать 5W-30',
                'manual_section_id' => $maintenanceSection?->id,
            ],
            [
                'maker' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2020,
                'mileage_interval' => 10000,
                'item' => 'Масляный фильтр',
                'recommendation' => 'Заменять масляный фильтр вместе с маслом каждые 10 000 км',
                'manual_section_id' => $maintenanceSection?->id,
            ],
            [
                'maker' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2020,
                'mileage_interval' => 40000,
                'item' => 'Свечи зажигания',
                'recommendation' => 'Заменять свечи зажигания каждые 40 000 км',
                'manual_section_id' => $engineSection?->id,
            ],

            // Honda Civic
            [
                'maker' => 'Honda',
                'model' => 'Civic',
                'year' => 2021,
                'mileage_interval' => 12000,
                'item' => 'Масло двигателя',
                'recommendation' => 'Менять масло каждые 12 000 км, использовать 0W-20',
                'manual_section_id' => $maintenanceSection?->id,
            ],
            [
                'maker' => 'Honda',
                'model' => 'Civic',
                'year' => 2021,
                'mileage_interval' => 24000,
                'item' => 'Воздушный фильтр',
                'recommendation' => 'Заменять воздушный фильтр каждые 24 000 км',
                'manual_section_id' => $maintenanceSection?->id,
            ],

            // Volkswagen Golf
            [
                'maker' => 'Volkswagen',
                'model' => 'Golf',
                'year' => 2019,
                'mileage_interval' => 15000,
                'item' => 'Масло двигателя',
                'recommendation' => 'Менять масло каждые 15 000 км, использовать 5W-40',
                'manual_section_id' => $maintenanceSection?->id,
            ],
            [
                'maker' => 'Volkswagen',
                'model' => 'Golf',
                'year' => 2019,
                'mileage_interval' => 60000,
                'item' => 'Топливный фильтр',
                'recommendation' => 'Заменять топливный фильтр каждые 60 000 км',
                'manual_section_id' => $maintenanceSection?->id,
            ],

            // BMW 3 Series
            [
                'maker' => 'BMW',
                'model' => '3 Series',
                'year' => 2022,
                'mileage_interval' => 12000,
                'item' => 'Масло двигателя',
                'recommendation' => 'Менять масло каждые 12 000 км, использовать 5W-30',
                'manual_section_id' => $maintenanceSection?->id,
            ],
            [
                'maker' => 'BMW',
                'model' => '3 Series',
                'year' => 2022,
                'mileage_interval' => 30000,
                'item' => 'Свечи зажигания',
                'recommendation' => 'Заменять свечи зажигания каждые 30 000 км',
                'manual_section_id' => $engineSection?->id,
            ],

            // Ford Focus
            [
                'maker' => 'Ford',
                'model' => 'Focus',
                'year' => 2020,
                'mileage_interval' => 15000,
                'item' => 'Масло двигателя',
                'recommendation' => 'Менять масло каждые 15 000 км, использовать 5W-20',
                'manual_section_id' => $maintenanceSection?->id,
            ],
            [
                'maker' => 'Ford',
                'model' => 'Focus',
                'year' => 2020,
                'mileage_interval' => 30000,
                'item' => 'Тормозная жидкость',
                'recommendation' => 'Заменять тормозную жидкость каждые 30 000 км',
                'manual_section_id' => $maintenanceSection?->id,
            ],
        ];

        foreach ($recommendations as $recommendation) {
            CarRecommendation::create($recommendation);
        }
    }
}