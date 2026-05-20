<?php

namespace Listeners\Link;

use App\Models\Link;
use App\Models\User;
use Hopla\HistoryManagement\Jobs\LogHistoriesJob;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class HandleLinkForceDeletedTest extends TenancyTestCase
{
    #[Test]
    public function test_handle_link_force_deleted(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->create([
            'user_id' => $user->id,
            'url' => 'http://example.com/force-deleted',
            'expired_at' => now()->addDays(10),
        ]);

        auth()->login($user);

        Queue::fake();

        $link->forceDelete();

        Queue::assertPushed(LogHistoriesJob::class);
    }
}
