<?php

namespace Tests\Unit\Hopla\GatewayManagement\Handlers;

use App\Models\Client;
use App\Models\User;
use Hopla\GatewayManagement\Handlers\GatewayUser;
use Tests\Utils\TenancyTestCase;

class GatewayUserTest extends TenancyTestCase
{
    private User $user;

    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->client = Client::where('tenant', 'test')->first();
    }

    public function test_get_user_from_wedrop_returns_200(): void
    {
        $this->actingAs($this->user, 'api');

        \Http::fake([
            '*' => \Http::response([
                'tenant' => 'test',
                'user_email' => $this->user->email,
            ]),
        ]);

        $response = (new GatewayUser)->getUserWeDrop();

        $this->assertEquals(200, $response->status());
        $this->assertEquals('test', $response['tenant']);
        $this->assertEquals($this->user->email, $response['user_email']);
    }

    public function test_get_user_from_wedrop_returns_404(): void
    {
        $this->actingAs($this->user, 'api');

        \Http::fake([
            '*' => \Http::response([], 404),
        ]);

        $response = (new GatewayUser)->getUserWeDrop();

        $this->assertEquals(404, $response->status());
    }
}
