<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // FREE подписка
        Subscription::updateOrCreate(
            ['name' => 'free'],
            [
                'display_name' => 'Free',
                'price' => 0, // бесплатно
                'duration_days' => 0, // бессрочно
                'max_vehicles' => 1,
                'max_reminders' => 5,
                'features' => [
                    'vehicles_management',
                    'basic_reminders',
                    'sto_search',
                    'advice',
                    'profile_settings',
                    'model_recommendations',
                    'expenses_statistics',
                    'expenses_history',
                ],
                'is_active' => true,
            ]
        );

        // PRO подписка
        Subscription::updateOrCreate(
            ['name' => 'pro'],
            [
                'display_name' => 'PRO',
                'price' => 499, // $4.99 в центах
                'duration_days' => 30, // месячная подписка
                'max_vehicles' => 3,
                'max_reminders' => null, // безлимит
                'features' => [
                    // Все функции FREE +
                    'photo_documents', // Фото документов для авто
                    'receipt_photos', // Фото чеков для трат
                    'pdf_export', // Экспорт отчетов в PDF
                    'unlimited_reminders', // Неограниченные напоминания
                    'expense_reminders', // Напоминания о добавлении трат (3 раза в неделю)
                ],
                'is_active' => true,
            ]
        );

        // PREMIUM подписка (в разработке)
        Subscription::updateOrCreate(
            ['name' => 'premium'],
            [
                'display_name' => 'Premium',
                'price' => 999, // $9.99 в центах (примерная цена)
                'duration_days' => 30,
                'max_vehicles' => 3,
                'max_reminders' => null, // безлимит
                'features' => [
                    // Все функции PRO +
                    'ai_assistant', // AI помощник
                    'trips', // Функционал поездки
                    'fuel_tracking', // Полный учет заправок
                    'mileage_tracking', // Ежедневный ввод пробега
                    'smart_reminders', // Умные напоминания (пробег + дата)
                    'cloud_storage', // Облачное хранилище
                ],
                'is_active' => false, // пока в разработке
            ]
        );
    }
}
