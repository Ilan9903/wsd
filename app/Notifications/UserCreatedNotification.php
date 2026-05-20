<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly User $user)
    {
        //
    }

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
        $frontUrl = config('wesend.domain');

        $domainUrl = tenant()?->id ? 'https://'.tenant()->id.$frontUrl : config('app.url').'/support';

        return (new MailMessage)
            ->subject(__('notifications.welcome_user.subject'))
            ->view('emails.template-email', [
                'greetingUser' => __('notifications.welcome_user.greeting', [
                    'name' => $this->user->first_name.' '.$this->user->last_name,
                ]),
                'intro1User' => __('notifications.welcome_user.intro_1'),
                'intro2User' => __('notifications.welcome_user.intro_2'),
                'lineEmailUser' => __('notifications.welcome_user.email', [
                    'email' => $this->user->email ?? __('notifications.welcome_user.not_provided'),
                ]),
                'linePhoneUser' => __('notifications.welcome_user.phone', [
                    'phone' => $this->user->phone ?? __('notifications.welcome_user.not_provided'),
                ]),
                'lineActionDomainUser' => __('notifications.welcome_user.domain_info'),
                'lineDomainUser' => $domainUrl,
                'lineQuestionsUser' => __('notifications.welcome_user.questions'),
                'salutationUser' => __('notifications.welcome_user.salutation'),
            ]);
    }
}
