<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TermsOfServiceController extends Controller
{
    /**
     * Get terms of service for specific language
     */
    public function show(Request $request, string $language): JsonResponse
    {
        $language = strtolower($language);
        
        // Validate language
        if (!in_array($language, ['ru', 'uk', 'en'])) {
            $language = 'en'; // fallback to English
        }

        $sections = $this->getFallbackContent($language);

        return response()->json([
            'success' => true,
            'language' => $language,
            'sections' => $sections,
        ]);
    }

    /**
     * Get fallback content for terms of service
     */
    private function getFallbackContent(string $language): array
    {
        $fallbackSections = [
            'ru' => [
                [
                    'section' => 'acceptance',
                    'title' => 'Принятие условий',
                    'content' => 'Используя приложение myGarage, вы соглашаетесь с настоящими Условиями использования. Если вы не согласны с этими условиями, пожалуйста, не используйте приложение.'
                ],
                [
                    'section' => 'subscriptions',
                    'title' => 'Подписки',
                    'content' => 'Приложение предлагает автоматически продлеваемые подписки. Подписки автоматически продлеваются, если не отменить их за 24 часа до окончания периода. Оплата производится через ваш аккаунт Apple ID или Google Play.'
                ],
                [
                    'section' => 'cancellation',
                    'title' => 'Отмена подписки',
                    'content' => 'Вы можете отменить подписку в любое время через настройки вашего Apple ID или Google Play. После отмены доступ к функциям подписки сохранится до конца оплаченного периода.'
                ],
                [
                    'section' => 'userContent',
                    'title' => 'Контент пользователя',
                    'content' => 'Вы несете ответственность за весь контент, который вы загружаете в приложение. Мы не претендуем на права собственности на ваш контент.'
                ],
                [
                    'section' => 'prohibitedUse',
                    'title' => 'Запрещенное использование',
                    'content' => 'Запрещается использовать приложение для незаконных целей, нарушения прав других лиц или распространения вредоносного контента.'
                ],
                [
                    'section' => 'limitation',
                    'title' => 'Ограничение ответственности',
                    'content' => 'Приложение предоставляется "как есть". Мы не гарантируем бесперебойную работу приложения и не несем ответственности за любые убытки, возникшие в результате использования приложения.'
                ],
                [
                    'section' => 'changes',
                    'title' => 'Изменения условий',
                    'content' => 'Мы оставляем за собой право изменять эти условия в любое время. О существенных изменениях мы уведомим пользователей через приложение.'
                ],
                [
                    'section' => 'contact',
                    'title' => 'Контакты',
                    'content' => 'По вопросам использования приложения обращайтесь по адресу: support@mygarage.uno'
                ]
            ],
            'uk' => [
                [
                    'section' => 'acceptance',
                    'title' => 'Прийняття умов',
                    'content' => 'Використовуючи додаток myGarage, ви погоджуєтеся з цими Умовами використання. Якщо ви не згодні з цими умовами, будь ласка, не використовуйте додаток.'
                ],
                [
                    'section' => 'subscriptions',
                    'title' => 'Підписки',
                    'content' => 'Додаток пропонує автоматично відновлювані підписки. Підписки автоматично продовжуються, якщо не скасувати їх за 24 години до закінчення періоду. Оплата здійснюється через ваш обліковий запис Apple ID або Google Play.'
                ],
                [
                    'section' => 'cancellation',
                    'title' => 'Скасування підписки',
                    'content' => 'Ви можете скасувати підписку в будь-який час через налаштування вашого Apple ID або Google Play. Після скасування доступ до функцій підписки збережеться до кінця оплаченого періоду.'
                ],
                [
                    'section' => 'userContent',
                    'title' => 'Контент користувача',
                    'content' => 'Ви несете відповідальність за весь контент, який ви завантажуєте в додаток. Ми не претендуємо на права власності на ваш контент.'
                ],
                [
                    'section' => 'prohibitedUse',
                    'title' => 'Заборонене використання',
                    'content' => 'Заборонено використовувати додаток для незаконних цілей, порушення прав інших осіб або поширення шкідливого контенту.'
                ],
                [
                    'section' => 'limitation',
                    'title' => 'Обмеження відповідальності',
                    'content' => 'Додаток надається "як є". Ми не гарантуємо безперебійну роботу додатку і не несемо відповідальності за будь-які збитки, що виникли в результаті використання додатку.'
                ],
                [
                    'section' => 'changes',
                    'title' => 'Зміни умов',
                    'content' => 'Ми залишаємо за собою право змінювати ці умови в будь-який час. Про істотні зміни ми повідомимо користувачів через додаток.'
                ],
                [
                    'section' => 'contact',
                    'title' => 'Контакти',
                    'content' => 'З питаннями використання додатку звертайтеся за адресою: support@mygarage.uno'
                ]
            ],
            'en' => [
                [
                    'section' => 'acceptance',
                    'title' => 'Acceptance of Terms',
                    'content' => 'By using the myGarage app, you agree to these Terms of Service. If you do not agree with these terms, please do not use the app.'
                ],
                [
                    'section' => 'subscriptions',
                    'title' => 'Subscriptions',
                    'content' => 'The app offers auto-renewing subscriptions. Subscriptions automatically renew unless cancelled at least 24 hours before the end of the period. Payment is charged through your Apple ID or Google Play account.'
                ],
                [
                    'section' => 'cancellation',
                    'title' => 'Cancellation',
                    'content' => 'You can cancel your subscription at any time through your Apple ID or Google Play settings. After cancellation, access to subscription features will remain until the end of the paid period.'
                ],
                [
                    'section' => 'userContent',
                    'title' => 'User Content',
                    'content' => 'You are responsible for all content you upload to the app. We do not claim ownership of your content.'
                ],
                [
                    'section' => 'prohibitedUse',
                    'title' => 'Prohibited Use',
                    'content' => 'It is prohibited to use the app for illegal purposes, violation of others\' rights, or distribution of harmful content.'
                ],
                [
                    'section' => 'limitation',
                    'title' => 'Limitation of Liability',
                    'content' => 'The app is provided "as is". We do not guarantee uninterrupted operation of the app and are not liable for any losses arising from the use of the app.'
                ],
                [
                    'section' => 'changes',
                    'title' => 'Changes to Terms',
                    'content' => 'We reserve the right to change these terms at any time. We will notify users about significant changes through the app.'
                ],
                [
                    'section' => 'contact',
                    'title' => 'Contact',
                    'content' => 'For questions about using the app, contact us at: support@mygarage.uno'
                ]
            ]
        ];

        return $fallbackSections[$language] ?? $fallbackSections['en'];
    }
}

