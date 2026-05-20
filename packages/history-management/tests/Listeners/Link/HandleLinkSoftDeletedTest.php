<?php

namespace Listeners\Link;

use App\Models\Link;
use App\Models\User;
use Hopla\HistoryManagement\Handlers\LogManager;
use Hopla\HistoryManagement\Listeners\Link\HandleLinkSoftDeleted;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class HandleLinkSoftDeletedTest extends TenancyTestCase
{
    private LogManager $logManager;

    private HandleLinkSoftDeleted $handler;

    private User $user;

    private Link $link;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logManager = new LogManager;
        $this->handler = new HandleLinkSoftDeleted($this->logManager);
        $this->user = User::factory()->create();
        $this->link = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com/soft-deleted',
            'expired_at' => now()->addDays(10),
        ]);
    }

    #[Test]
    public function test_handle_link_soft_deleted(): void
    {
        auth()->login($this->user);

        $this->link->delete();

        $this->handler->handle($this->link);

        $this->assertDatabaseHas('histories', [
            'user_id' => $this->user->id,
            'link_id' => $this->link->id,
            'description' => __('log.remove-link', [
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'link_url' => $this->link->url,
            ]),
        ]);
    }
}
