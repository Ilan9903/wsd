<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorActivationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected string $token) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.2fa-activate.activate'))
            ->line(__('notifications.2fa-activate.for-activate'))
            ->action(__('notifications.2fa-activate.activate-now'), 'https://'.tenant()->id.config('frontend.domain')."/activate-2fa?token={$this->token}")
            ->line(__('notifications.2fa-activate.link-expire-15m'));
    }
}
