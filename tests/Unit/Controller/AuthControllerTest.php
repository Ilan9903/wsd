<?php

namespace Tests\Unit\Controller;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PragmaRX\Google2FA\Google2FA;
use Tests\Utils\TenancyTestCase;

#[Group('controller')]
#[Group('auth')]
class AuthControllerTest extends TenancyTestCase
{
    private string $urlLogin;

    private string $urlLogout;

    private string $url2FA;

    private string $refresh;

    private User $user;

    private User $userTrash;

    private User $userTwoFactorSecret;

    protected function setUp(): void
    {
        parent::setUp();
        $google2fa = new Google2FA;
        $tenant = tenant()->id;
        $this->user = User::factory()->create();
        $this->userTwoFactorSecret = User::factory()->create([
            'two_factor_secret' => $google2fa->generateSecretKey(),
            'two_factor_verified_at' => now(),
        ]);
        $this->userTrash = User::factory()->create([
            'deleted_at' => now(),
        ]);
        $this->urlLogin = 'https://'.$tenant.config('wesend.domainApi').'/api/login';
        $this->urlLogout = 'https://'.$tenant.config('wesend.domainApi').'/api/logout';
        $this->url2FA = 'https://'.$tenant.config('wesend.domainApi').'/api/2FA/login';
        $this->refresh = 'https://'.$tenant.config('wesend.domainApi').'/api/refresh';
    }

    #[Test]
    public function test_login(): void
    {
        $response = $this->postJson($this->urlLogin, [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'id',
            'first_name',
            'last_name',
            'image',
            'token',
            'city',
            'country',
            'phone',
            'viewAdminPanel',
            'viewThemePanel',
        ]);
    }

    #[Test]
    public function test_logout(): void
    {
        $token = auth('api')->login($this->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson($this->urlLogout);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => __('auth.logout-success'),
        ]);
    }

    #[Test]
    public function test_login_invalid_password(): void
    {
        $response = $this->postJson($this->urlLogin, [
            'email' => $this->user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
        $response->assertExactJson([
            'message' => __('auth.invalid-credentials.'),
        ]);
    }

    #[Test]
    public function test_login_invalid_email(): void
    {
        $response = $this->postJson($this->urlLogin, [
            'email' => 'wrongemail@fzczf.fr',
            'password' => 'password',
        ]);

        $response->assertStatus(401);
        $response->assertExactJson([
            'message' => __('auth.invalid-credentials.'),
        ]);
    }

    #[Test]
    public function login_trashed_user()
    {
        $response = $this->postJson($this->urlLogin, [
            'email' => $this->userTrash->email,
            'password' => 'password',
        ]);

        $response->assertStatus(401);

    }

    #[Test]
    public function login_two_factor_secret_user()
    {

        $response = $this->postJson($this->urlLogin, [
            'email' => $this->userTwoFactorSecret->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'two_factor' => true,
        ]);
    }

    #[Test]
    public function test_verify_2fa_invalid_token()
    {
        $uuid = $this->userTwoFactorSecret->id;
        $encrypted = Crypt::encryptString($uuid);
        Cache::put('2fa_temp_token_'.$uuid.'1', true, now()->addMinutes(5));

        $response = $this->postJson($this->url2FA, [
            'otp' => '000000', // mauvais code
            'temp_token' => $encrypted,
            'tenant' => 'test',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => __('auth.expired-invalid-token'),
        ]);
    }

    #[Test]
    public function test_verify_2fa_invalid_code()
    {
        $uuid = $this->userTwoFactorSecret->id;
        $encrypted = Crypt::encryptString($uuid);
        Cache::put('2fa_temp_token_'.$uuid, true, now()->addMinutes(5));

        $response = $this->postJson($this->url2FA, [
            'otp' => '000000', // mauvais code
            'temp_token' => $encrypted,
            'tenant' => 'test',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => __('auth.invalid-otp'),
        ]);
    }

    #[Test]
    public function test_verify_2fa_success()
    {
        $validOtp = (new Google2FA)->getCurrentOtp($this->userTwoFactorSecret->two_factor_secret);

        $uuid = $this->userTwoFactorSecret->id;
        $encrypted = Crypt::encryptString($uuid);
        Cache::put('2fa_temp_token_'.$uuid, true, now()->addMinutes(5));

        $response = $this->postJson($this->url2FA, [
            'otp' => $validOtp,
            'temp_token' => $encrypted,
            'tenant' => 'test',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'id',
            'first_name',
            'last_name',
            'image',
            'token',
            'city',
            'country',
            'phone',
            'viewAdminPanel',
            'viewThemePanel',
        ]);
    }

    #[Test]
    public function it_refreshes_a_valid_jwt_token()
    {

        $token = auth('api')->login($this->user);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->postJson($this->refresh);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'first_name',
                'last_name',
                'profilPicture',
                'token',
                'viewAdminPanel',
                'viewThemePanel',
                'expires_in',
            ]);

        $this->assertEquals($this->user->id, $response->json('id'));
        $this->assertNotEquals($token, $response->json('token'));
    }

    #[Test]
    public function it_fails_without_token()
    {

        $response = $this->postJson($this->refresh);

        $response->assertStatus(401);
    }

    #[Test]
    public function test_too_many_wrong_login(): void
    {
        $key = 'login:'.$this->user->email;

        RateLimiter::clear($key);

        $payload = [
            'email' => $this->user->email,
            'password' => 'wrong-password',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->postJson($this->urlLogin, $payload)
                ->assertStatus(401);
        }

        $response = $this->postJson($this->urlLogin, $payload);

        $response->assertStatus(429)
            ->assertJson([
                'message' => __('auth.throttle', [
                    'seconds' => 1200,
                ]),
            ]);
    }
}
