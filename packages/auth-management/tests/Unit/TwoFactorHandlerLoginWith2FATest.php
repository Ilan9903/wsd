<?php

namespace Tests\Unit;

use App\Models\User;
use Hopla\AuthManagement\Handlers\TwoFactorHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class TwoFactorHandlerLoginWith2FATest extends TenancyTestCase
{
    #[Test]
    public function test_it_generates_and_stores_temp_token_for_2fa_login()
    {
        Cache::clear();
        $handler = new TwoFactorHandler;
        $user = User::factory()->create();

        $response = $handler->loginWith2FA($user);
        $data = $response->getData(true);

        $this->assertTrue($data['two_factor']);
        $this->assertEquals($user->id, $data['user_id']);
        $this->assertArrayHasKey('temp_token', $data);

        $this->assertNotEquals($user->id, $data['temp_token']);
        $this->assertEquals($user->id, Crypt::decryptString($data['temp_token']));

        $cacheKey = "2fa_temp_token_{$user->id}";
        $this->assertTrue(Cache::has($cacheKey));
        $this->assertEquals($data['temp_token'], Cache::get($cacheKey));
    }
}
