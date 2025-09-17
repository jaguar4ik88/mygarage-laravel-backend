<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FaqCategory;
use App\Models\FaqCategoryTranslation;
use App\Models\FaqQuestion;
use App\Models\FaqQuestionTranslation;

class FaqUkrainianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add Ukrainian translations for categories
        $categoryTranslations = [
            'general' => 'Загальні питання',
            'vehicles' => 'Автомобілі',
            'reminders' => 'Нагадування',
            'history' => 'Історія',
            'manual' => 'Руководство',
            'technical' => 'Технічні питання',
        ];

        foreach ($categoryTranslations as $key => $ukrainianName) {
            $category = FaqCategory::where('key', $key)->first();
            if ($category) {
                FaqCategoryTranslation::updateOrCreate(
                    [
                        'faq_category_id' => $category->id,
                        'locale' => 'uk',
                    ],
                    ['name' => $ukrainianName]
                );
            }
        }

        // Add Ukrainian translations for questions
        $questionTranslations = [
            // General questions
            1 => [
                'question' => 'Що таке MyGarage?',
                'answer' => 'MyGarage - це додаток для управління вашими автомобілями, який допомагає відстежувати технічне обслуговування, нагадування та історію ремонтів.'
            ],
            2 => [
                'question' => 'Як додати автомобіль?',
                'answer' => 'Натисніть кнопку "Додати автомобіль" на головному екрані. Ви можете додати автомобіль за VIN-кодом або вручну, вказавши марку, модель та рік.'
            ],
            3 => [
                'question' => 'Як змінити мову додатку?',
                'answer' => 'Перейдіть до профілю → Налаштування → Мова та виберіть потрібну мову зі списку.'
            ],
            // Vehicle questions
            4 => [
                'question' => 'Чи можна додати кілька автомобілів?',
                'answer' => 'Так, ви можете додати необмежену кількість автомобілів у додаток.'
            ],
            5 => [
                'question' => 'Як оновити пробіг автомобіля?',
                'answer' => 'На сторінці деталей автомобіля натисніть на поточний пробіг, введіть нове значення та збережіть.'
            ],
            // Reminder questions
            6 => [
                'question' => 'Як створити нагадування?',
                'answer' => 'Перейдіть до розділу "Нагадування" та натисніть "Додати нагадування". Виберіть тип обслуговування, вкажіть дати та пробіг.'
            ],
            7 => [
                'question' => 'Як відредагувати нагадування?',
                'answer' => 'Натисніть на нагадування в списку, внесіть зміни та збережіть.'
            ],
            // History questions
            8 => [
                'question' => 'Як додати запис в історію?',
                'answer' => 'Перейдіть до розділу "Історія" та натисніть "Додати запис". Заповніть інформацію про проведене обслуговування.'
            ],
            // Manual questions
            9 => [
                'question' => 'Де знайти керівництво по автомобілю?',
                'answer' => 'Виберіть автомобіль на головному екрані та перейдіть до розділу "Керівництво". Там ви знайдете корисні поради та рекомендації.'
            ],
            // Technical questions
            10 => [
                'question' => 'Додаток не працює, що робити?',
                'answer' => 'Спробуйте перезапустити додаток. Якщо проблема зберігається, перевірте підключення до інтернету або зверніться до служби підтримки.'
            ],
            11 => [
                'question' => 'Як синхронізувати дані?',
                'answer' => 'Дані синхронізуються автоматично за наявності підключення до інтернету. При першому запуску додатку дані завантажуються з сервера.'
            ],
        ];

        foreach ($questionTranslations as $questionId => $translation) {
            FaqQuestionTranslation::updateOrCreate(
                [
                    'faq_question_id' => $questionId,
                    'locale' => 'uk',
                ],
                [
                    'question' => $translation['question'],
                    'answer' => $translation['answer'],
                ]
            );
        }
    }
}
