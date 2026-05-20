<?php

namespace Listeners\Link;

use App\Models\Link;
use App\Models\User;
use Hopla\HistoryManagement\Handlers\LogManager;
use Hopla\HistoryManagement\Listeners\Link\HandleLinkUpdated;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class HandleLinkUpdatedTest extends TenancyTestCase
{
    private LogManager $logManager;

    private HandleLinkUpdated $handler;

    private User $user;

    private Link $link;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logManager = new LogManager;
        $this->handler = new HandleLinkUpdated($this->logManager);
        $this->user = User::factory()->create();
        $this->link = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com/link-updated',
            'expired_at' => now()->addDays(10),
        ]);
    }

    #[Test]
    public function test_handle_link_updated(): void
    {
        auth()->login($this->user);

        $this->link->update([
            'url' => 'http://example.com/link-updated-success',
        ]);

        $this->handler->handle($this->link);

        $this->assertDatabaseHas('histories', [
            'user_id' => $this->user->id,
            'link_id' => $this->link->id,
            'description' => __('log.update-link', [
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'link_url' => $this->link->url,
            ]),
        ]);
    }
}
