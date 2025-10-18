<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     */
    public string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = $this->getResetUrl($notifiable);

        return (new MailMessage)
            ->subject('MyGarage - Сброс пароля')
            ->greeting('Привет!')
            ->line('Вы получили это письмо, потому что мы получили запрос на сброс пароля для вашего аккаунта.')
            ->line('Ваш код для сброса пароля:')
            ->line('**' . $this->token . '**')
            ->line('Этот код истечет через 60 минут.')
            ->line('Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.')
            ->salutation('С уважением, команда MyGarage');
    }

    /**
     * Get the reset URL for the given notifiable.
     */
    protected function getResetUrl($notifiable): string
    {
        // For mobile app, we just return token
        // The mobile app will handle the reset flow
        return config('app.url') . '/password/reset?token=' . $this->token . '&email=' . urlencode($notifiable->getEmailForPasswordReset());
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'token' => $this->token,
        ];
    }
}

