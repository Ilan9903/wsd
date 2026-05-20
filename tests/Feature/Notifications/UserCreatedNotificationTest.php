<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TestCase;

class UserCreatedNotificationTest extends TestCase
{
    #[Test]
    public function test_sends_mail_with_correct_view_and_subject(): void
    {
        Notification::fake();

        $user = User::factory()->make([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '0123456789',
        ]);

        $notification = new UserCreatedNotification($user);

        $this->assertEquals(['mail'], $notification->via($user));

        $mailMessage = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals(__('notifications.welcome_user.subject'), $mailMessage->subject);

        $viewData = $mailMessage->viewData;

        $this->assertArrayHasKey('greetingUser', $viewData);
        $this->assertEquals(
            __('notifications.welcome_user.greeting', ['name' => $user->first_name.' '.$user->last_name]),
            $viewData['greetingUser']
        );

        $this->assertArrayHasKey('lineEmailUser', $viewData);
        $this->assertEquals(
            __('notifications.welcome_user.email', ['email' => $user->email]),
            $viewData['lineEmailUser']
        );

        $this->assertArrayHasKey('linePhoneUser', $viewData);
        $this->assertEquals(
            __('notifications.welcome_user.phone', ['phone' => $user->phone]),
            $viewData['linePhoneUser']
        );

        $this->assertArrayHasKey('lineDomainUser', $viewData);
        $expectedDomain = tenant()?->id ? 'https://'.tenant()->id.config('wesend.domain') : config('app.url').'/support';
        $this->assertEquals($expectedDomain, $viewData['lineDomainUser']);
    }
}
