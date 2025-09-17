<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Reminder;
use App\Models\ServiceHistory;
use App\Models\ServiceStation;
use Carbon\Carbon;

class AdditionalTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем первого пользователя (тестового)
        $user = User::first();
        
        if (!$user) {
            $this->command->error('No user found. Please run TestDataSeeder first.');
            return;
        }

        $this->command->info('Adding additional test data for user: ' . $user->name);

        // Добавляем еще 3 автомобиля для тестового пользователя
        $additionalVehicles = [
            [
                'user_id' => $user->id,
                'vin' => '1HGBH41JXMN109' . rand(100, 999),
                'year' => 2021,
                'make' => 'BMW',
                'model' => 'X5',
                'engine_type' => 'V6 Turbo',
                'mileage' => 45000,
                'image_url' => null,
            ],
            [
                'user_id' => $user->id,
                'vin' => '1FTFW1ET5DFC' . rand(10000, 99999),
                'year' => 2018,
                'make' => 'Ford',
                'model' => 'F-150',
                'engine_type' => 'V8',
                'mileage' => 120000,
                'image_url' => null,
            ],
            [
                'user_id' => $user->id,
                'vin' => 'WBAFR9C50CC' . rand(100000, 999999),
                'year' => 2022,
                'make' => 'Mercedes-Benz',
                'model' => 'C-Class',
                'engine_type' => 'I4 Turbo',
                'mileage' => 15000,
                'image_url' => null,
            ],
        ];

        foreach ($additionalVehicles as $vehicleData) {
            Vehicle::create($vehicleData);
        }

        $this->command->info('Added 3 additional vehicles');

        // Получаем все автомобили пользователя
        $vehicles = $user->vehicles;

        // Добавляем больше напоминаний для каждого автомобиля
        $reminderTypes = ['filters', 'tires', 'transmission', 'timing_belt', 'oil_change', 'brake_pads', 'battery', 'coolant'];
        
        foreach ($vehicles as $vehicle) {
            // Создаем 3-4 напоминания для каждого автомобиля
            $remindersCount = rand(3, 4);
            
            for ($i = 0; $i < $remindersCount; $i++) {
                $type = $reminderTypes[array_rand($reminderTypes)];
                $lastServiceDate = Carbon::now()->subDays(rand(30, 365));
                $nextServiceDate = $lastServiceDate->copy()->addDays(rand(30, 180));
                
                Reminder::create([
                    'vehicle_id' => $vehicle->id,
                    'type' => $type,
                    'title' => $this->getReminderTitle($type),
                    'description' => $this->getReminderDescription($type),
                    'last_service_date' => $lastServiceDate,
                    'last_service_mileage' => $vehicle->mileage - rand(5000, 15000),
                    'next_service_mileage' => $vehicle->mileage + rand(5000, 15000),
                    'next_service_date' => $nextServiceDate,
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('Added additional reminders for all vehicles');

        // Добавляем больше записей истории обслуживания
        foreach ($vehicles as $vehicle) {
            $serviceCount = rand(2, 4);
            
            for ($i = 0; $i < $serviceCount; $i++) {
                $serviceDate = Carbon::now()->subDays(rand(1, 365));
                $serviceTypes = ['oil_change', 'tire_rotation', 'brake_service', 'transmission_service', 'engine_repair', 'suspension_repair'];
                
                ServiceHistory::create([
                    'vehicle_id' => $vehicle->id,
                    'type' => $serviceTypes[array_rand($serviceTypes)],
                    'title' => $this->getServiceTitle($serviceTypes[array_rand($serviceTypes)]),
                    'description' => $this->getServiceDescription($serviceTypes[array_rand($serviceTypes)]),
                    'cost' => rand(5000, 50000) / 100, // Стоимость от 50 до 500 рублей
                    'mileage' => $vehicle->mileage - rand(1000, 20000),
                    'service_date' => $serviceDate,
                    'station_name' => $this->getRandomStationName(),
                ]);
            }
        }

        $this->command->info('Added additional service history records');

        // Добавляем больше СТО
        $additionalStations = [
            [
                'name' => 'Автосервис "Мастер"',
                'address' => 'ул. Ленина, 15, Москва',
                'phone' => '+7 (495) 123-45-67',
                'rating' => 4.2,
                'distance' => 2.5,
                'latitude' => 55.7558,
                'longitude' => 37.6176,
                'types' => json_encode(['repair', 'maintenance', 'tires']),
            ],
            [
                'name' => 'СТО "Быстрый ремонт"',
                'address' => 'пр. Мира, 45, Москва',
                'phone' => '+7 (495) 234-56-78',
                'rating' => 3.8,
                'distance' => 5.2,
                'latitude' => 55.7619,
                'longitude' => 37.6200,
                'types' => json_encode(['repair', 'diagnostics']),
            ],
            [
                'name' => 'Автоцентр "Премиум"',
                'address' => 'ул. Тверская, 8, Москва',
                'phone' => '+7 (495) 345-67-89',
                'rating' => 4.7,
                'distance' => 1.8,
                'latitude' => 55.7580,
                'longitude' => 37.6100,
                'types' => json_encode(['maintenance', 'tires', 'repair', 'diagnostics']),
            ],
            [
                'name' => 'Гараж "У дяди Васи"',
                'address' => 'ул. Садовая, 22, Москва',
                'phone' => '+7 (495) 456-78-90',
                'rating' => 4.0,
                'distance' => 3.1,
                'latitude' => 55.7500,
                'longitude' => 37.6000,
                'types' => json_encode(['repair', 'maintenance']),
            ],
        ];

        foreach ($additionalStations as $stationData) {
            ServiceStation::create($stationData);
        }

        $this->command->info('Added 4 additional service stations');

        // Выводим итоговую статистику
        $this->command->info('=== Final Statistics ===');
        $this->command->info('Total users: ' . User::count());
        $this->command->info('Total vehicles: ' . Vehicle::count());
        $this->command->info('Total reminders: ' . Reminder::count());
        $this->command->info('Total service history records: ' . ServiceHistory::count());
        $this->command->info('Total service stations: ' . ServiceStation::count());
    }

    private function getReminderTitle($type): string
    {
        $titles = [
            'filters' => 'Замена воздушного фильтра',
            'tires' => 'Замена шин',
            'transmission' => 'Обслуживание коробки передач',
            'timing_belt' => 'Замена ремня ГРМ',
            'oil_change' => 'Замена масла',
            'brake_pads' => 'Замена тормозных колодок',
            'battery' => 'Замена аккумулятора',
            'coolant' => 'Замена охлаждающей жидкости',
        ];

        return $titles[$type] ?? 'Техническое обслуживание';
    }

    private function getReminderDescription($type): string
    {
        $descriptions = [
            'filters' => 'Рекомендуется заменить воздушный фильтр для улучшения работы двигателя',
            'tires' => 'Проверьте состояние шин и при необходимости замените их',
            'transmission' => 'Обслуживание коробки передач для обеспечения плавной работы',
            'timing_belt' => 'Критически важно заменить ремень ГРМ для предотвращения поломки двигателя',
            'oil_change' => 'Регулярная замена моторного масла для защиты двигателя',
            'brake_pads' => 'Проверьте износ тормозных колодок и при необходимости замените',
            'battery' => 'Проверьте состояние аккумулятора и при необходимости замените',
            'coolant' => 'Замените охлаждающую жидкость для предотвращения перегрева двигателя',
        ];

        return $descriptions[$type] ?? 'Плановое техническое обслуживание';
    }

    private function getServiceTitle($type): string
    {
        $titles = [
            'oil_change' => 'Замена моторного масла',
            'tire_rotation' => 'Ротация шин',
            'brake_service' => 'Обслуживание тормозной системы',
            'transmission_service' => 'Обслуживание коробки передач',
            'engine_repair' => 'Ремонт двигателя',
            'suspension_repair' => 'Ремонт подвески',
        ];

        return $titles[$type] ?? 'Техническое обслуживание';
    }

    private function getServiceDescription($type): string
    {
        $descriptions = [
            'oil_change' => 'Замена моторного масла и масляного фильтра',
            'tire_rotation' => 'Перестановка шин для равномерного износа',
            'brake_service' => 'Проверка и замена тормозных колодок и дисков',
            'transmission_service' => 'Замена масла в коробке передач',
            'engine_repair' => 'Диагностика и ремонт неисправностей двигателя',
            'suspension_repair' => 'Замена амортизаторов и других элементов подвески',
        ];

        return $descriptions[$type] ?? 'Выполнено техническое обслуживание';
    }

    private function getRandomStationName(): string
    {
        $stations = [
            'Автосервис "Мастер"',
            'СТО "Быстрый ремонт"',
            'Автоцентр "Премиум"',
            'Гараж "У дяди Васи"',
            'Автосервис "Надежный"',
            'СТО "Профи"',
            'Автоцентр "Эксперт"',
        ];

        return $stations[array_rand($stations)];
    }
}