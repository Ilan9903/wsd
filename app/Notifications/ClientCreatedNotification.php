<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientCreatedNotification extends Notification
{
    use Queueable;

    /**
     * @param  Client  $client
     */
    public function __construct(protected Client $client) {}

    /**
     * @param  mixed  $notifiable
     * @return string[]
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $frontRoute = 'https://'.$this->client->name.config('wesend.domain');

        return (new MailMessage)
            ->subject(__('notifications.welcome.subject'))
            ->view('emails.template-email', [
                'greetingClient' => __('notifications.welcome.greeting', [
                    'name' => $this->client->name,
                ]),
                'lineIntroClient' => __('notifications.welcome.intro'),
                'lineNameClient' => __('notifications.welcome.name', [
                    'name' => $this->client->name,
                ]),
                'lineEmailClient' => __('notifications.welcome.email', [
                    'email' => $this->client->email,
                ]),
                'linePhoneClient' => __('notifications.welcome.phone', [
                    'phone' => $this->client->phone_number ?? __('notifications.welcome.not_provided'),
                ]),
                'lineAddressClient' => __('notifications.welcome.address', [
                    'address' => $this->client->address ?? __('notifications.welcome.not_provided'),
                ]),
                'lineDomInfoClient' => __('notifications.welcome.domain_info'),
                'lineLoginClient' => $frontRoute,
                'lineQuestionsClient' => __('notifications.welcome.questions'),
                'salutationClient' => __('notifications.welcome.salutation'),
            ]);
    }
}
