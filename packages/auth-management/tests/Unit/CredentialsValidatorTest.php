<?php

namespace Tests\Unit;

use App\Models\TenantConfig;
use App\Models\TenantContract;
use App\Models\User;
use Hopla\AuthManagement\Handlers\CredentialsValidator;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class CredentialsValidatorTest extends TenancyTestCase
{
    protected CredentialsValidator $validator;

    private TenantContract $contract;

    private TenantConfig $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new CredentialsValidator;
        $this->contract = TenantContract::first();
        $this->config = TenantConfig::first();
    }

    #[Test]
    public function test_it_validates_correct_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        $this->assertTrue($this->validator->isValid($user, 'secret123'));
    }

    #[Test]
    public function test_it_fails_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        $this->assertFalse($this->validator->isValid($user, 'wrongpass'));
    }

    #[Test]
    public function test_it_detects_2fa_enabled()
    {
        $user = User::factory()->create([
            'two_factor_verified_at' => now(),
        ]);

        $this->assertTrue($this->validator->is2FAEnabled($user));
    }

    #[Test]
    public function test_it_detects_2fa_required()
    {
        $this->contract->update(['is_health' => true]);
        $this->contract->refresh();
        $user = User::factory()->create([
            'two_factor_verified_at' => null,
        ]);

        $this->assertTrue($this->validator->is2FARequired($user));
    }

    #[Test]
    public function test_it_detects_password_expired()
    {
        $this->config->update([
            'isPasswordShouldRenew' => true,
            'passwordExpirationDelay' => 30,
        ]);
        $this->config->refresh();
        $user = User::factory()->create([
            'password_last_update_at' => now()->subDays(40),
        ]);

        $this->assertTrue($this->validator->isPasswordExpired($user));
    }

    #[Test]
    public function test_it_detects_password_not_expired()
    {
        $this->config->update([
            'isPasswordShouldRenew' => true,
            'passwordExpirationDelay' => 30,
        ]);
        $this->config->refresh();
        $user = User::factory()->create([
            'password_last_update_at' => now()->subDays(10),
        ]);

        $this->assertFalse($this->validator->isPasswordExpired($user));
    }

    #[Test]
    public function test_it_returns_false_if_password_renewal_is_disabled()
    {
        $this->config->update([
            'isPasswordShouldRenew' => false,
        ]);
        $this->config->refresh();
        $user = User::factory()->create([
            'password_last_update_at' => now()->subDays(999),
        ]);

        $this->assertFalse($this->validator->isPasswordExpired($user));
    }

    protected function tearDown(): void
    {
        $this->config->update([
            'isPasswordShouldRenew' => false,
            'passwordExpirationDelay' => 45,
        ]);
        $this->contract->update(['is_health' => false]);
        parent::tearDown();
    }
}
