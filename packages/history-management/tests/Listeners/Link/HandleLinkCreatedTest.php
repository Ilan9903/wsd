<?php

namespace Listeners\Link;

use App\Models\Link;
use App\Models\User;
use Hopla\HistoryManagement\Handlers\LogManager;
use Hopla\HistoryManagement\Listeners\Link\HandleLinkCreated;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class HandleLinkCreatedTest extends TenancyTestCase
{
    private LogManager $logManager;

    private HandleLinkCreated $handler;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logManager = new LogManager;
        $this->handler = new HandleLinkCreated($this->logManager);
        $this->user = User::factory()->create();
    }

    #[Test]
    public function test_handle_link_created(): void
    {
        auth()->login($this->user);

        $link = Link::create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com/link-created',
            'expired_at' => now()->addDays(10),
        ]);

        $this->handler->handle($link);

        $this->assertDatabaseHas('histories', [
            'user_id' => $this->user->id,
            'link_id' => $link->id,
            'description' => __('log.created-link', [
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'link_url' => $link->url,
            ]),
        ]);
    }
}
