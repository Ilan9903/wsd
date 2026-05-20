<?php

namespace Tests\Unit;

use App\Models\User;
use Hopla\AuthManagement\Handlers\TwoFactorHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class TwoFactorHandlerAskEnable2FATest extends TenancyTestCase
{
    #[Test]
    public function test_it_sends_2fa_activation_notification_and_stores_temp_token()
    {
        Cache::clear();
        $handler = new TwoFactorHandler;

        $user = User::factory()->create();

        $response = $handler->askEnable2FA($user);

        $data = $response->getData(true);

        $this->assertEquals(__('auth.user-2fa-ask-enable-email'), $data['message']);

        $cacheKey = "2fa_temp_token_{$user->id}";
        $this->assertTrue(Cache::has($cacheKey));

        $encrypted = Cache::get($cacheKey);
        $this->assertEquals($user->id, Crypt::decryptString($encrypted));
    }
}
