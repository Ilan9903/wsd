<?php

namespace Tests\Feature\Notifications;

use App\Models\Client;
use App\Notifications\ClientCreatedNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TestCase;

class ClientCreatedNotificationTest extends TestCase
{
    #[Test]
    public function test_sends_mail_with_correct_view_and_subject(): void
    {
        Notification::fake();

        $client = Client::factory()->make([
            'name' => 'MyClient',
            'email' => 'client@example.com',
            'phone_number' => '0123456789',
            'address' => '123 Rue Exemple',
        ]);

        $notification = new ClientCreatedNotification($client);

        $this->assertEquals(['mail'], $notification->via($client));

        $mailMessage = $notification->toMail($client);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals(__('notifications.welcome.subject'), $mailMessage->subject);

        $this->assertArrayHasKey('greetingClient', $mailMessage->viewData);
        $this->assertEquals(
            __('notifications.welcome.greeting', ['name' => $client->name]),
            $mailMessage->viewData['greetingClient']
        );

        $this->assertArrayHasKey('lineLoginClient', $mailMessage->viewData);
        $this->assertEquals(
            'https://'.$client->name.config('wesend.domain'),
            $mailMessage->viewData['lineLoginClient']
        );
    }
}
