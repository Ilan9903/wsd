<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Notifications\InformUserOfGroupAssignement;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TestCase;

class InformUserOfGroupAssignementTest extends TestCase
{
    #[Test]
    public function test_sends_mail_with_correct_view_and_subject(): void
    {
        Notification::fake();

        $user = User::factory()->make([
            'first_name' => 'John',
            'email' => 'john@example.com',
        ]);

        $groupName = 'AdminGroup';

        $notification = new InformUserOfGroupAssignement($groupName);

        $this->assertEquals(['mail'], $notification->via($user));

        $mailMessage = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals(__('notifications.group_added.subject'), $mailMessage->subject);

        $this->assertArrayHasKey('greetingGroup', $mailMessage->viewData);
        $this->assertEquals(
            __('notifications.group_added.greeting', ['name' => $user->first_name]),
            $mailMessage->viewData['greetingGroup']
        );

        $this->assertArrayHasKey('lineGroup', $mailMessage->viewData);
        $this->assertEquals(
            __('notifications.group_added.line_1', ['group' => $groupName]),
            $mailMessage->viewData['lineGroup']
        );

        $this->assertArrayHasKey('footerGroup', $mailMessage->viewData);
        $this->assertEquals(
            __('notifications.group_added.footer'),
            $mailMessage->viewData['footerGroup']
        );

        $this->assertArrayHasKey('salutationGroup', $mailMessage->viewData);
        $this->assertEquals(
            __('notifications.group_added.salutation'),
            $mailMessage->viewData['salutationGroup']
        );
    }
}
