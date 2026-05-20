<?php

namespace App\Notifications;

use App\Models\Link;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DownloadLink extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Link $link
    ) {}

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
    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.link_download.subject'))
            ->view('emails.template-email', [
                'greetingLink' => __('notifications.link_download.greeting', [
                    'first_name' => $notifiable->first_name,
                    'last_name' => $notifiable->last_name,
                ]),
                'lineLink' => __('notifications.link_download.line', [
                    'created_at' => $this->link->created_at,
                ]),
                'salutationLink' => __('notifications.link_download.salutation'),
            ]);
    }
}
