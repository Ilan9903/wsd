<?php

namespace Listeners\User;

use App\Models\User;
use Hopla\HistoryManagement\Handlers\LogManager;
use Hopla\HistoryManagement\Jobs\LogHistoriesJob;
use Hopla\HistoryManagement\Listeners\User\HandleUserLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TestCase;

class HandleUserLoginTest extends TestCase
{
    #[Test]
    public function test_handle_user_login(): void
    {
        $user = User::factory()->create();

        Queue::fake();

        Event::fake();

        $listener = new HandleUserLogin(new LogManager);
        $listener->handle(new Login('api', $user, false));

        Queue::assertPushed(LogHistoriesJob::class);
    }
}
