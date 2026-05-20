<?php

namespace Tests\Unit\Hopla\GatewayManagement\Unit\Controller;

use App\Models\Client;
use App\Models\FileOrFolder;
use App\Models\Link;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Hopla\GatewayManagement\Facade\GatewayManager;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Spatie\WebhookServer\WebhookCall;
use Tests\Utils\TenancyTestCase;

class GatewayControllerTest extends TenancyTestCase
{
    private User $user;

    private Client $client;

    private Link $linkFOF;

    private FileOrFolder $fileOrFolder;

    protected function setUp(): void
    {
        parent::setUp();

        config(['gateway-management.webhook_secret' => 'test-secret']);

        $this->user = User::factory()->create();

        $this->client = Client::where('tenant', 'test')->firstOrFail();

        $this->linkFOF = Link::factory()->create([
            'user_id' => $this->user->id,
            'expired_at' => now()->addDays(15),
        ]);

        $this->fileOrFolder = FileOrFolder::factory()->create([
            'user_id' => $this->user->id,
            'link_id' => $this->linkFOF->id,
        ]);
    }

    #[Test]
    public function test_send_data_file_returns_200_with_file(): void
    {
        Queue::fake();

        $this->actingAs($this->user, 'api');

        GatewayManager::shouldReceive('getUserWeDrop')->once()->andReturn([
            'user_email' => $this->user->email,
            'tenant' => $this->client->tenant,
        ]);

        $webhookMock = \Mockery::mock(WebhookCall::class);
        $webhookMock->shouldReceive('create')->andReturnSelf();
        $webhookMock->shouldReceive('url')->andReturnSelf();
        $webhookMock->shouldReceive('onQueue')->andReturnSelf();
        $webhookMock->shouldReceive('doNotVerifySsl')->andReturnSelf();
        $webhookMock->shouldReceive('payload')->andReturnSelf();
        $webhookMock->shouldReceive('useSecret')->andReturn(config('gateway-management.webhook_secret'));
        $webhookMock->shouldReceive('dispatch')->andReturnNull();

        $url = 'http://'.$this->client->tenant.config('wesend.domainApi').'/api/webhook-link-files';

        $this->postJson($url, ['link_url' => $this->linkFOF->url])
            ->assertOk()
            ->assertJson([
                'message' => __('gateway.files-sent'),
            ]);
    }

    #[Test]
    public function test_send_data_file_returns_404_without_file(): void
    {
        Queue::fake();

        $this->actingAs($this->user, 'api');

        GatewayManager::shouldReceive('getUserWeDrop')->once()->andReturn([
            'tenant' => $this->client->tenant,
            'user_email' => $this->user->email,
        ]);

        $webhookMock = \Mockery::mock(WebhookCall::class);
        $webhookMock->shouldReceive('create')->andReturnSelf();
        $webhookMock->shouldReceive('url')->andReturnSelf();
        $webhookMock->shouldReceive('onQueue')->andReturnSelf();
        $webhookMock->shouldReceive('doNotVerifySsl')->andReturnSelf();
        $webhookMock->shouldReceive('payload')->andReturnSelf();
        $webhookMock->shouldReceive('useSecret')->andReturn(config('gateway-management.webhook_secret'));
        $webhookMock->shouldReceive('dispatch')->andReturnNull();

        $url = 'http://'.$this->client->tenant.config('wesend.domainApi').'/api/webhook-link-files';

        $this->postJson($url)->assertStatus(404);
    }

    #[Test]
    public function test_get_user_from_we_drop_returns_200_found(): void
    {
        $this->actingAs($this->user, 'api');

        GatewayManager::shouldReceive('getUserWeDrop')->once()->andReturn(
            new \Illuminate\Http\Client\Response(
                new Response(200, [], json_encode([
                    'user_email' => $this->user->email,
                    'tenant' => $this->client->tenant,
                ]))
            )
        );

        $url = 'http://'.$this->client->tenant.config('wesend.domainApi').'/api/get-user-wdp';

        $this->postJson($url)
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'tenant_wdp',
                'user_email',
            ]);
    }

    #[Test]
    public function test_get_user_from_we_drop_returns_404_not_found(): void
    {
        $this->actingAs($this->user, 'api');

        GatewayManager::shouldReceive('getUserWeDrop')->once()->andReturn(
            new \Illuminate\Http\Client\Response(
                new Response(404, [], json_encode([]))
            )
        );

        $url = 'http://'.$this->client->tenant.config('wesend.domainApi').'/api/get-user-wdp';

        $this->postJson($url)->assertStatus(404);
    }

    protected function tearDown(): void
    {
        $this->linkFOF->delete();

        $this->fileOrFolder->delete();

        $this->user->delete();

        parent::tearDown();
    }
}
