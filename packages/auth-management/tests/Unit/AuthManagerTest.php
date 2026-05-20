<?php

namespace Tests\Unit;

use App\Models\TenantConfig;
use App\Models\TenantContract;
use App\Models\User;
use Hopla\AuthManagement\Handlers\AuthManager;
use Hopla\AuthManagement\Handlers\CredentialsValidator;
use Hopla\AuthManagement\Handlers\PasswordRenewalHandler;
use Hopla\AuthManagement\Handlers\TwoFactorHandler;
use Hopla\AuthManagement\Handlers\UserLoginHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class AuthManagerTest extends TenancyTestCase
{
    private TenantContract $contract;

    private TenantConfig $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contract = TenantContract::first();
        $this->config = TenantConfig::first();
    }

    protected function createAuthManager(): AuthManager
    {
        return new AuthManager(
            new CredentialsValidator,
            new TwoFactorHandler,
            new PasswordRenewalHandler,
            new UserLoginHandler
        );
    }

    #[Test]
    public function test_it_triggers_2fa_if_enabled()
    {
        $user = User::factory()->create([
            'two_factor_verified_at' => now(),
            'password' => bcrypt('secret'),
        ]);

        $auth = $this->createAuthManager();

        $response = $auth->handleLogin(['email' => $user->email, 'password' => 'secret']);
        $data = $response->getData(true);

        $this->assertTrue($data['two_factor']);
        $this->assertEquals($user->id, Crypt::decryptString($data['temp_token']));
    }

    #[Test]
    public function test_it_triggers_ask_enable_2fa_if_required()
    {
        $this->contract->update(['is_health' => true]);
        $this->contract->refresh();
        $user = User::factory()->create([
            'two_factor_verified_at' => null,
            'password' => bcrypt('secret'),
        ]);

        $auth = $this->createAuthManager();

        $response = $auth->handleLogin(['email' => $user->email, 'password' => 'secret']);
        $data = $response->getData(true);

        $this->assertEquals(__('auth.user-2fa-ask-enable-email'), $data['message']);
        $this->assertTrue(Cache::has("2fa_temp_token_{$user->id}"));
    }

    #[Test]
    public function test_it_triggers_password_renewal_if_expired()
    {
        $this->config->update([
            'isPasswordShouldRenew' => true,
            'passwordExpirationDelay' => 0,
        ]);
        $this->config->refresh();
        $user = User::factory()->create([
            'password_last_update_at' => now()->subDays(10),
            'password' => bcrypt('secret'),
        ]);

        $auth = $this->createAuthManager();

        $response = $auth->handleLogin(['email' => $user->email, 'password' => 'secret']);
        $data = $response->getData(true);

        $this->assertTrue($data['renew_password']);
        $this->assertEquals($user->id, Crypt::decryptString($data['temp_token']));
    }

    #[Test]
    public function test_it_logs_in_user_successfully()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
        ]);

        $auth = $this->createAuthManager();

        $response = $auth->handleLogin(['email' => $user->email, 'password' => 'secret']);
        $data = $response->getData(true);

        $this->assertEquals(__('auth.login-success'), $data['message']);
        $this->assertEquals($user->id, $data['id']);
        $this->assertArrayHasKey('token', $data);
    }

    protected function tearDown(): void
    {

        $this->contract->update(['is_health' => false]);
        parent::tearDown();
    }
}
