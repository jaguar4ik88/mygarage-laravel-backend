<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Reminder;
use App\Models\ServiceHistory;
use App\Models\ServiceStation;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create test users
        $user1 = User::create([
            'name' => 'Алексей Иванов',
            'email' => 'alexey@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user2 = User::create([
            'name' => 'Мария Петрова',
            'email' => 'maria@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Create vehicles for user1
        $vehicle1 = Vehicle::create([
            'user_id' => $user1->id,
            'vin' => '1HGBH41JXMN109186',
            'year' => 2020,
            'make' => 'Toyota',
            'model' => 'Camry',
            'engine_type' => '2.5L Hybrid',
            'mileage' => 45000,
        ]);

        $vehicle2 = Vehicle::create([
            'user_id' => $user1->id,
            'vin' => '2HGBH41JXMN109187',
            'year' => 2019,
            'make' => 'Honda',
            'model' => 'Civic',
            'engine_type' => '1.5L Turbo',
            'mileage' => 62000,
        ]);

        // Create vehicles for user2
        $vehicle3 = Vehicle::create([
            'user_id' => $user2->id,
            'vin' => '3HGBH41JXMN109188',
            'year' => 2021,
            'make' => 'BMW',
            'model' => 'X3',
            'engine_type' => '2.0L Turbo',
            'mileage' => 28000,
        ]);

        $vehicle4 = Vehicle::create([
            'user_id' => $user2->id,
            'vin' => '4HGBH41JXMN109189',
            'year' => 2018,
            'make' => 'Audi',
            'model' => 'A4',
            'engine_type' => '2.0L TFSI',
            'mileage' => 85000,
        ]);

        // Create reminders for vehicle1
        Reminder::create([
            'vehicle_id' => $vehicle1->id,
            'type' => 'oil',
            'title' => 'Замена моторного масла',
            'description' => 'Плановое ТО - замена моторного масла и фильтра',
            'last_service_date' => '2024-10-01',
            'last_service_mileage' => 42000,
            'next_service_mileage' => 48000,
            'next_service_date' => '2025-01-15',
            'is_active' => true,
        ]);

        Reminder::create([
            'vehicle_id' => $vehicle1->id,
            'type' => 'inspection',
            'title' => 'Техосмотр',
            'description' => 'Плановый техосмотр автомобиля',
            'last_service_date' => '2024-08-15',
            'last_service_mileage' => 41000,
            'next_service_mileage' => 50000,
            'next_service_date' => '2025-02-15',
            'is_active' => true,
        ]);

        // Create reminders for vehicle2
        Reminder::create([
            'vehicle_id' => $vehicle2->id,
            'type' => 'oil',
            'title' => 'Замена моторного масла',
            'description' => 'Плановое ТО - замена моторного масла',
            'last_service_date' => '2024-09-01',
            'last_service_mileage' => 58000,
            'next_service_mileage' => 65000,
            'next_service_date' => '2025-01-01',
            'is_active' => true,
        ]);

        // Create service history
        ServiceHistory::create([
            'vehicle_id' => $vehicle1->id,
            'type' => 'maintenance',
            'title' => 'Плановое ТО',
            'description' => 'Замена моторного масла, масляного фильтра, воздушного фильтра',
            'cost' => 8500.00,
            'mileage' => 42000,
            'service_date' => '2024-10-01',
            'station_name' => 'Автосервис "Мастер"',
        ]);

        ServiceHistory::create([
            'vehicle_id' => $vehicle2->id,
            'type' => 'maintenance',
            'title' => 'Плановое ТО',
            'description' => 'Замена моторного масла, фильтров, проверка систем',
            'cost' => 7500.00,
            'mileage' => 58000,
            'service_date' => '2024-09-01',
            'station_name' => 'Honda Center',
        ]);

        // Create service stations
        ServiceStation::create([
            'name' => 'Автосервис "Мастер"',
            'address' => 'ул. Ленина, 123, Москва',
            'phone' => '+7 (495) 123-45-67',
            'rating' => 4.5,
            'distance' => 2.3,
            'latitude' => 55.7558,
            'longitude' => 37.6176,
            'types' => ['ТО', 'Ремонт', 'Диагностика'],
        ]);

        ServiceStation::create([
            'name' => 'Toyota Center',
            'address' => 'ул. Тверская, 15, Москва',
            'phone' => '+7 (495) 555-01-01',
            'rating' => 4.8,
            'distance' => 3.2,
            'latitude' => 55.7558,
            'longitude' => 37.6176,
            'types' => ['ТО', 'Ремонт', 'Диагностика', 'Официальный дилер'],
        ]);

        ServiceStation::create([
            'name' => 'Honda Center',
            'address' => 'пр. Садовое кольцо, 78, Москва',
            'phone' => '+7 (495) 555-02-02',
            'rating' => 4.7,
            'distance' => 4.1,
            'latitude' => 55.7558,
            'longitude' => 37.6176,
            'types' => ['ТО', 'Ремонт', 'Диагностика', 'Официальный дилер'],
        ]);
    }
}