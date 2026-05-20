<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InformUserOfGroupAssignement extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $groupName
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
            ->subject(__('notifications.group_added.subject'))
            ->view('emails.template-email', [
                'greetingGroup' => __('notifications.group_added.greeting', [
                    'name' => $notifiable->first_name,
                ]),
                'lineGroup' => __('notifications.group_added.line_1', [
                    'group' => $this->groupName,
                ]),
                'footerGroup' => __('notifications.group_added.footer'),
                'salutationGroup' => __('notifications.group_added.salutation'),
            ]);
    }
}
