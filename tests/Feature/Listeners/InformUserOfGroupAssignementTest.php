<?php

namespace Tests\Feature\Listeners;

use App\Listeners\Eloquent\UserHasGroup\InformUserOfGroupAssignement;
use App\Models\Group;
use App\Models\User;
use App\Models\UserHasGroup;
use App\Notifications\InformUserOfGroupAssignement as InformUserOfGroupAssignementNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class InformUserOfGroupAssignementTest extends TenancyTestCase
{
    #[Test]
    public function test_notifies_user_when_assigned_to_a_group(): void
    {
        Mail::fake();
        Notification::fake();

        $user = User::factory()->create();
        $group = Group::factory()->create(['name' => 'Admins']);

        $user->groups()->attach($group->id);

        $userHasGroup =
            UserHasGroup::where('user_id', $user->id)
                ->where('group_id', $group->id)->first();

        $listener = new InformUserOfGroupAssignement;
        $listener->handle($userHasGroup);

        $this->assertDatabaseHas('user_has_group', [
            'user_id' => $user->id,
            'group_id' => $group->id,
        ]);

        Notification::assertSentTo(
            $user,
            InformUserOfGroupAssignementNotification::class,
            fn ($notification, $channels) => in_array('mail', $channels)
        );

    }
}
