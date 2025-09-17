<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VehicleManual;
use App\Models\User;

class UserManualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user (assuming there's at least one user)
        $user = User::first();
        
        if (!$user) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Create some custom manuals for the user
        $userManuals = [
            [
                'title' => 'Мои личные заметки',
                'content' => 'Здесь я записываю свои наблюдения по обслуживанию автомобиля. Важно проверять масло каждые 5000 км.',
                'icon' => 'note',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Контакты сервисов',
                'content' => 'Автосервис "Мотор": +380501234567\nСТО "АвтоМир": +380509876543\nЭвакуатор: +380501112233',
                'icon' => 'phone',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Специфичные процедуры',
                'content' => 'Для моей модели автомобиля важно:\n1. Заменять воздушный фильтр каждые 15000 км\n2. Проверять тормозные колодки каждые 10000 км\n3. Сезонная смена шин в октябре и апреле',
                'icon' => 'settings',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Полезные ссылки',
                'content' => 'Официальный сайт производителя: https://example.com\nФорум владельцев: https://forum.example.com\nИнструкция по эксплуатации: https://manual.example.com',
                'icon' => 'link',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($userManuals as $index => $manualData) {
            VehicleManual::create([
                'user_id' => $user->id,
                'vehicle_id' => 1, // Use first vehicle as placeholder
                'section_id' => 'user_manual_' . ($index + 1), // Generate unique section_id
                'title' => $manualData['title'],
                'content' => $manualData['content'],
                'icon' => $manualData['icon'],
                'sort_order' => $manualData['sort_order'],
                'is_active' => $manualData['is_active'],
            ]);
        }

        $this->command->info('User manuals created successfully!');
    }
}