<?php

namespace Listeners\User;

use App\Models\User;
use Hopla\HistoryManagement\Handlers\LogManager;
use Hopla\HistoryManagement\Jobs\LogHistoriesJob;
use Hopla\HistoryManagement\Listeners\User\HandleUserLogout;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TestCase;

class HandleUserLogoutTest extends TestCase
{
    #[Test]
    public function test_handle_user_login(): void
    {
        $user = User::factory()->create();

        Queue::fake();

        Event::fake();

        $listener = new HandleUserLogout(new LogManager);
        $listener->handle(new Logout('api', $user));

        Queue::assertPushed(LogHistoriesJob::class);
    }
}
