<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Notifications\TwoFactorActivationNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class TwoFactorActivationNotificationTest extends TenancyTestCase
{
    #[Test]
    public function test_sends_mail_with_correct_subject_and_action(): void
    {
        Notification::fake();

        $user = User::factory()->make();

        $token = '1234567890';
        $tenantId = 'test';

        $notification = new TwoFactorActivationNotification($token);

        $this->assertEquals(['mail'], $notification->via($user));

        $mailMessage = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals(__('notifications.2fa-activate.activate'), $mailMessage->subject);

        $this->assertContains(__('notifications.2fa-activate.for-activate'), $mailMessage->introLines);
        $this->assertContains(__('notifications.2fa-activate.link-expire-15m'), $mailMessage->outroLines);

        $expectedUrl = 'https://'.$tenantId.config('frontend.domain')."/activate-2fa?token={$token}";
        $this->assertEquals($expectedUrl, $mailMessage->actionUrl);
        $this->assertEquals(__('notifications.2fa-activate.activate-now'), $mailMessage->actionText);
    }
}
