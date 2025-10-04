<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrivacyPolicy;

class PrivacyPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'section' => 'dataCollection',
                'ru' => [
                    'title' => 'Сбор данных',
                    'content' => 'Мы собираем только необходимую информацию для работы приложения: данные о ваших автомобилях, напоминания, историю обслуживания и основную информацию профиля.'
                ],
                'uk' => [
                    'title' => 'Збір даних',
                    'content' => 'Ми збираємо тільки необхідну інформацію для роботи додатку: дані про ваші автомобілі, нагадування, історію обслуговування та основну інформацію профілю.'
                ],
                'en' => [
                    'title' => 'Data Collection',
                    'content' => 'We collect only the necessary information for the app to function: data about your vehicles, reminders, service history and basic profile information.'
                ]
            ],
            [
                'section' => 'dataUsage',
                'ru' => [
                    'title' => 'Использование данных',
                    'content' => 'Ваши данные используются для предоставления функций приложения, отправки напоминаний и улучшения пользовательского опыта. Мы не продаем ваши данные третьим лицам.'
                ],
                'uk' => [
                    'title' => 'Використання даних',
                    'content' => 'Ваші дані використовуються для надання функцій додатку, надсилання нагадувань та покращення користувацького досвіду. Ми не продаємо ваші дані третім особам.'
                ],
                'en' => [
                    'title' => 'Data Usage',
                    'content' => 'Your data is used to provide app features, send reminders and improve user experience. We do not sell your data to third parties.'
                ]
            ],
            [
                'section' => 'dataSharing',
                'ru' => [
                    'title' => 'Передача данных',
                    'content' => 'Мы не передаем ваши личные данные третьим лицам, за исключением случаев, когда это требуется по закону или для обеспечения безопасности.'
                ],
                'uk' => [
                    'title' => 'Передача даних',
                    'content' => 'Ми не передаємо ваші персональні дані третім особам, за винятком випадків, коли це вимагається законом або для забезпечення безпеки.'
                ],
                'en' => [
                    'title' => 'Data Sharing',
                    'content' => 'We do not share your personal data with third parties, except when required by law or for security purposes.'
                ]
            ],
            [
                'section' => 'dataSecurity',
                'ru' => [
                    'title' => 'Безопасность данных',
                    'content' => 'Мы используем современные методы шифрования и безопасности для защиты ваших данных. Все данные передаются по защищенному соединению HTTPS.'
                ],
                'uk' => [
                    'title' => 'Безпека даних',
                    'content' => 'Ми використовуємо сучасні методи шифрування та безпеки для захисту ваших даних. Всі дані передаються по захищеному з\'єднанню HTTPS.'
                ],
                'en' => [
                    'title' => 'Data Security',
                    'content' => 'We use modern encryption and security methods to protect your data. All data is transmitted over secure HTTPS connection.'
                ]
            ],
            [
                'section' => 'userRights',
                'ru' => [
                    'title' => 'Права пользователя',
                    'content' => 'Вы можете в любое время запросить доступ к своим данным, их изменение или удаление. Для этого обратитесь к нам через форму обратной связи.'
                ],
                'uk' => [
                    'title' => 'Права користувача',
                    'content' => 'Ви можете в будь-який час запросити доступ до своїх даних, їх зміну або видалення. Для цього зверніться до нас через форму зворотного зв\'язку.'
                ],
                'en' => [
                    'title' => 'User Rights',
                    'content' => 'You can request access to your data, its modification or deletion at any time. To do this, contact us through the feedback form.'
                ]
            ],
            [
                'section' => 'contact',
                'ru' => [
                    'title' => 'Контакты',
                    'content' => 'По вопросам конфиденциальности обращайтесь по адресу: privacy@mygarage.uno'
                ],
                'uk' => [
                    'title' => 'Контакти',
                    'content' => 'З питаннями конфіденційності звертайтеся за адресою: privacy@mygarage.uno'
                ],
                'en' => [
                    'title' => 'Contact',
                    'content' => 'For privacy questions, contact us at: privacy@mygarage.uno'
                ]
            ],
            [
                'section' => 'changes',
                'ru' => [
                    'title' => 'Изменения политики',
                    'content' => 'Мы можем обновлять эту политику конфиденциальности. О существенных изменениях мы уведомим пользователей через приложение.'
                ],
                'uk' => [
                    'title' => 'Зміни політики',
                    'content' => 'Ми можемо оновлювати цю політику конфіденційності. Про істотні зміни ми повідомимо користувачів через додаток.'
                ],
                'en' => [
                    'title' => 'Policy Changes',
                    'content' => 'We may update this privacy policy. We will notify users about significant changes through the app.'
                ]
            ]
        ];

        foreach ($sections as $index => $section) {
            foreach (['ru', 'uk', 'en'] as $language) {
                PrivacyPolicy::create([
                    'language' => $language,
                    'section' => $section['section'],
                    'title' => $section[$language]['title'],
                    'content' => $section[$language]['content'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
}