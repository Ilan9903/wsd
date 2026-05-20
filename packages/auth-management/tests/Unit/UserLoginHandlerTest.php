<?php

namespace Tests\Unit;

use App\Models\TenantContract;
use App\Models\User;
use Hopla\AuthManagement\Handlers\UserLoginHandler;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class UserLoginHandlerTest extends TenancyTestCase
{
    private TenantContract $contract;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contract = TenantContract::first();
    }

    #[Test]
    public function test_it_logs_in_the_user_and_returns_expected_json()
    {

        $this->contract->update([
            'is_health' => true,
        ]);
        $this->contract->refresh();
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'city' => 'Paris',
            'country' => 'France',
            'phone' => '+33123456789',
        ]);

        $handler = new UserLoginHandler;

        $response = $handler->login($user);

        $json = $response->getData(true);

        $this->assertEquals(__('auth.login-success'), $json['message']);
        $this->assertEquals($user->id, $json['id']);
        $this->assertEquals('John', $json['first_name']);
        $this->assertEquals('Doe', $json['last_name']);
        $this->assertEquals('Paris', $json['city']);
        $this->assertEquals('France', $json['country']);
        $this->assertEquals('+33123456789', $json['phone']);

        $this->assertIsString($json['token']);
        $this->assertNotEmpty($json['token']);

        $this->assertFalse($json['viewAdminPanel']);
        $this->assertFalse($json['viewThemePanel']);

        $this->assertEquals($this->contract->is_health, $json['is_health']);
    }

    protected function tearDown(): void
    {
        $this->contract->update(['is_health' => false]);
        parent::tearDown();
    }
}
