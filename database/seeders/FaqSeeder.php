<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FaqCategory;
use App\Models\FaqCategoryTranslation;
use App\Models\FaqQuestion;
use App\Models\FaqQuestionTranslation;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'general' => [
                'icon' => 'help-circle',
                'translations' => [
                    'uk' => 'Загальні питання',
                    'ru' => 'Общие вопросы',
                    'en' => 'General Questions',
                ],
                'questions' => [
                    [
                        'translations' => [
                            'uk' => [
                                'question' => 'Що таке MyGarage?',
                                'answer' => 'MyGarage - це додаток для управління вашими автомобілями, який допомагає відстежувати технічне обслуговування, нагадування та історію ремонтів.'
                            ],
                            'ru' => [
                                'question' => 'Что такое MyGarage?',
                                'answer' => 'MyGarage - это приложение для управления вашими автомобилями, которое помогает отслеживать техническое обслуживание, напоминания и историю ремонтов.'
                            ],
                            'en' => [
                                'question' => 'What is MyGarage?',
                                'answer' => 'MyGarage is an app for managing your vehicles that helps track maintenance, reminders, and repair history.'
                            ]
                        ]
                    ],
                    [
                        'translations' => [
                            'ru' => [
                                'question' => 'Как добавить автомобиль?',
                                'answer' => 'Нажмите кнопку "Добавить автомобиль" на главном экране. Вы можете добавить автомобиль по VIN-коду или вручную, указав марку, модель и год.'
                            ],
                            'en' => [
                                'question' => 'How to add a vehicle?',
                                'answer' => 'Click the "Add Vehicle" button on the home screen. You can add a vehicle by VIN code or manually by specifying make, model and year.'
                            ]
                        ]
                    ],
                    [
                        'translations' => [
                            'ru' => [
                                'question' => 'Как изменить язык приложения?',
                                'answer' => 'Перейдите в профиль → Настройки → Язык и выберите нужный язык из списка.'
                            ],
                            'en' => [
                                'question' => 'How to change app language?',
                                'answer' => 'Go to Profile → Settings → Language and select the desired language from the list.'
                            ]
                        ]
                    ]
                ]
            ],
            'vehicles' => [
                'icon' => 'car',
                'translations' => [
                    'ru' => 'Автомобили',
                    'en' => 'Vehicles',
                ],
                'questions' => [
                    [
                        'translations' => [
                            'ru' => [
                                'question' => 'Можно ли добавить несколько автомобилей?',
                                'answer' => 'Да, вы можете добавить неограниченное количество автомобилей в приложение.'
                            ],
                            'en' => [
                                'question' => 'Can I add multiple vehicles?',
                                'answer' => 'Yes, you can add unlimited number of vehicles to the app.'
                            ]
                        ]
                    ],
                    [
                        'translations' => [
                            'ru' => [
                                'question' => 'Как обновить пробег автомобиля?',
                                'answer' => 'На странице деталей автомобиля нажмите на текущий пробег, введите новое значение и сохраните.'
                            ],
                            'en' => [
                                'question' => 'How to update vehicle mileage?',
                                'answer' => 'On the vehicle details page, click on the current mileage, enter the new value and save.'
                            ]
                        ]
                    ]
                ]
            ],
            'reminders' => [
                'icon' => 'bell',
                'translations' => [
                    'ru' => 'Напоминания',
                    'en' => 'Reminders',
                ],
                'questions' => [
                    [
                        'translations' => [
                            'ru' => [
                                'question' => 'Как создать напоминание?',
                                'answer' => 'Перейдите в раздел "Напоминания" и нажмите "Добавить напоминание". Выберите тип обслуживания, укажите даты и пробег.'
                            ],
                            'en' => [
                                'question' => 'How to create a reminder?',
                                'answer' => 'Go to the "Reminders" section and click "Add Reminder". Select the service type, specify dates and mileage.'
                            ]
                        ]
                    ],
                    [
                        'translations' => [
                            'ru' => [
                                'question' => 'Как отредактировать напоминание?',
                                'answer' => 'Нажмите на напоминание в списке, внесите изменения и сохраните.'
                            ],
                            'en' => [
                                'question' => 'How to edit a reminder?',
                                'answer' => 'Click on the reminder in the list, make changes and save.'
                            ]
                        ]
                    ]
                ]
            ],
            'history' => [
                'icon' => 'history',
                'translations' => [
                    'ru' => 'История',
                    'en' => 'History',
                ],
                'questions' => [
                    [
                        'translations' => [
                            'ru' => [
                                'question' => 'Как добавить запись в историю?',
                                'answer' => 'Перейдите в раздел "История" и нажмите "Добавить запись". Заполните информацию о проведенном обслуживании.'
                            ],
                            'en' => [
                                'question' => 'How to add a history record?',
                                'answer' => 'Go to the "History" section and click "Add Record". Fill in the information about the service performed.'
                            ]
                        ]
                    ]
                ]
            ],
            'manual' => [
                'icon' => 'book',
                'translations' => [
                    'ru' => 'Руководство',
                    'en' => 'Manual',
                ],
                'questions' => [
                    [
                        'translations' => [
                            'ru' => [
                                'question' => 'Где найти руководство по автомобилю?',
                                'answer' => 'Выберите автомобиль на главном экране и перейдите в раздел "Руководство". Там вы найдете полезные советы и рекомендации.'
                            ],
                            'en' => [
                                'question' => 'Where to find vehicle manual?',
                                'answer' => 'Select a vehicle on the home screen and go to the "Manual" section. There you will find useful tips and recommendations.'
                            ]
                        ]
                    ]
                ]
            ],
            'technical' => [
                'icon' => 'settings',
                'translations' => [
                    'ru' => 'Технические вопросы',
                    'en' => 'Technical Questions',
                ],
                'questions' => [
                    [
                        'translations' => [
                            'ru' => [
                                'question' => 'Приложение не работает, что делать?',
                                'answer' => 'Попробуйте перезапустить приложение. Если проблема сохраняется, проверьте подключение к интернету или обратитесь в службу поддержки.'
                            ],
                            'en' => [
                                'question' => 'App is not working, what to do?',
                                'answer' => 'Try restarting the app. If the problem persists, check your internet connection or contact support.'
                            ]
                        ]
                    ],
                    [
                        'translations' => [
                            'ru' => [
                                'question' => 'Как синхронизировать данные?',
                                'answer' => 'Данные синхронизируются автоматически при наличии подключения к интернету. При первом запуске приложения данные загружаются с сервера.'
                            ],
                            'en' => [
                                'question' => 'How to sync data?',
                                'answer' => 'Data syncs automatically when internet connection is available. On first app launch, data is loaded from the server.'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        foreach ($categories as $key => $categoryData) {
            $category = FaqCategory::create([
                'key' => $key,
                'icon' => $categoryData['icon'],
                'is_active' => true,
                'sort_order' => array_search($key, array_keys($categories)),
            ]);

            // Add translations
            foreach ($categoryData['translations'] as $locale => $name) {
                FaqCategoryTranslation::create([
                    'faq_category_id' => $category->id,
                    'locale' => $locale,
                    'name' => $name,
                ]);
            }

            // Add questions
            foreach ($categoryData['questions'] as $questionIndex => $questionData) {
                $question = FaqQuestion::create([
                    'faq_category_id' => $category->id,
                    'is_active' => true,
                    'sort_order' => $questionIndex,
                ]);

                // Add question translations
                foreach ($questionData['translations'] as $locale => $translation) {
                    FaqQuestionTranslation::create([
                        'faq_question_id' => $question->id,
                        'locale' => $locale,
                        'question' => $translation['question'],
                        'answer' => $translation['answer'],
                    ]);
                }
            }
        }
    }
}
