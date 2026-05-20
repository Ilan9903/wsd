<?php

namespace Jobs;

use App\Models\History;
use App\Models\Link;
use App\Models\User;
use Hopla\HistoryManagement\Enums\HistoryActionType;
use Hopla\HistoryManagement\Enums\HistoryModelType;
use Hopla\HistoryManagement\Jobs\LogHistoriesJob;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class LogHistoriesJobTest extends TenancyTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function test_it_create_user_history(): void
    {
        Event::fake();

        $this->user = User::factory()->create();

        $job = new LogHistoriesJob(
            $this->user,
            null,
            HistoryModelType::USER,
            HistoryActionType::LOGIN,
            __('log.login-success', [
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
            ]),
            '127.0.0.1',
        );

        $job->handle();

        $history = History::latest()->first();

        $this->assertEquals($this->user->id, $history->user_id);
        $this->assertNull($history->link_id);
        $this->assertEquals(HistoryModelType::USER->value, $history->model_type);
        $this->assertEquals(HistoryActionType::LOGIN->value, $history->action);
        $this->assertEquals(__('log.login-success', [
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
        ]), $history->description);
        $this->assertEquals('127.0.0.1', $history->ip_address);
    }

    #[Test]
    public function test_it_create_link_history(): void
    {
        Link::flushEventListeners();

        $link = Link::factory()->create([
            'user_id' => $this->user->id,
            'url' => 'http://example.com/user-created',
            'expired_at' => now()->addDays(10),
        ]);

        $job = new LogHistoriesJob(
            $this->user,
            $link,
            HistoryModelType::LINK,
            HistoryActionType::LINK_CREATED,
            __('log.created-link', [
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'link_url' => $link->url,
            ]),
            '127.0.0.1',
        );

        $job->handle();

        $this->assertDatabaseHas('histories', [
            'user_id' => $this->user->id,
            'link_id' => $link->id,
            'model_type' => HistoryModelType::LINK->value,
            'action' => HistoryActionType::LINK_CREATED->value,
            'description' => __('log.created-link', [
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'link_url' => $link->url,
            ]),
            'ip_address' => '127.0.0.1',
        ]);
    }
}
