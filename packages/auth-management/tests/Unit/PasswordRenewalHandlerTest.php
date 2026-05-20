<?php

namespace Tests\Unit;

use App\Models\User;
use Hopla\AuthManagement\Handlers\PasswordRenewalHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class PasswordRenewalHandlerTest extends TenancyTestCase
{
    #[Test]
    public function test_it_generates_and_stores_a_temp_token_for_password_renewal()
    {

        Cache::clear();
        $handler = new PasswordRenewalHandler;
        $user = User::factory()->create();

        $response = $handler->renewPassword($user);
        $data = $response->getData(true);

        $this->assertTrue($data['renew_password']);
        $this->assertEquals($user->id, $data['user_id']);
        $this->assertArrayHasKey('temp_token', $data);

        $this->assertNotEquals($user->id, $data['temp_token']);
        $this->assertEquals($user->id, Crypt::decryptString($data['temp_token']));

        $cacheKey = "renew_password_temp_token_{$user->id}";
        $this->assertTrue(Cache::has($cacheKey));
        $this->assertEquals($data['temp_token'], Cache::get($cacheKey));
    }
}
