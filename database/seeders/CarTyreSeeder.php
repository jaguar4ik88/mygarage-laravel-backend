<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarTyre;

class CarTyreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tyres = [
            // Toyota Corolla
            [
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2020,
                'dimension' => '205/55 R16',
                'notes' => 'Можно ставить RunFlat шины',
            ],
            [
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2021,
                'dimension' => '205/55 R16',
                'notes' => 'Рекомендуется использовать зимние шины в холодное время года',
            ],

            // Honda Civic
            [
                'brand' => 'Honda',
                'model' => 'Civic',
                'year' => 2021,
                'dimension' => '215/50 R17',
                'notes' => 'Спортивные шины улучшают управляемость',
            ],
            [
                'brand' => 'Honda',
                'model' => 'Civic',
                'year' => 2022,
                'dimension' => '215/50 R17',
                'notes' => 'Экономичные шины снижают расход топлива',
            ],

            // Volkswagen Golf
            [
                'brand' => 'Volkswagen',
                'model' => 'Golf',
                'year' => 2019,
                'dimension' => '225/45 R17',
                'notes' => 'Премиум шины для лучшего сцепления',
            ],
            [
                'brand' => 'Volkswagen',
                'model' => 'Golf',
                'year' => 2020,
                'dimension' => '225/45 R17',
                'notes' => 'Всесезонные шины подходят для умеренного климата',
            ],

            // BMW 3 Series
            [
                'brand' => 'BMW',
                'model' => '3 Series',
                'year' => 2022,
                'dimension' => '225/50 R17',
                'notes' => 'Высокопроизводительные шины для спортивной езды',
            ],
            [
                'brand' => 'BMW',
                'model' => '3 Series',
                'year' => 2023,
                'dimension' => '225/50 R17',
                'notes' => 'RunFlat шины входят в стандартную комплектацию',
            ],

            // Ford Focus
            [
                'brand' => 'Ford',
                'model' => 'Focus',
                'year' => 2020,
                'dimension' => '205/60 R16',
                'notes' => 'Комфортные шины для городской езды',
            ],
            [
                'brand' => 'Ford',
                'model' => 'Focus',
                'year' => 2021,
                'dimension' => '205/60 R16',
                'notes' => 'Экологичные шины с низким сопротивлением качению',
            ],

            // Mercedes-Benz C-Class
            [
                'brand' => 'Mercedes-Benz',
                'model' => 'C-Class',
                'year' => 2021,
                'dimension' => '225/45 R18',
                'notes' => 'Премиум шины для люксового седана',
            ],
            [
                'brand' => 'Mercedes-Benz',
                'model' => 'C-Class',
                'year' => 2022,
                'dimension' => '225/45 R18',
                'notes' => 'Высокоскоростные шины с отличной управляемостью',
            ],

            // Audi A4
            [
                'brand' => 'Audi',
                'model' => 'A4',
                'year' => 2020,
                'dimension' => '225/50 R17',
                'notes' => 'Кватро-шины для полного привода',
            ],
            [
                'brand' => 'Audi',
                'model' => 'A4',
                'year' => 2021,
                'dimension' => '225/50 R17',
                'notes' => 'Зимние шины с улучшенным сцеплением на снегу',
            ],
        ];

        foreach ($tyres as $tyre) {
            CarTyre::create($tyre);
        }
    }
}