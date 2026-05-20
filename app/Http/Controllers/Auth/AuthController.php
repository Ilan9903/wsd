<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hopla\AuthManagement\Facades\AuthManager;
use Hopla\AuthManagement\Http\Requests\Login2FARequest;
use Hopla\AuthManagement\Http\Requests\LoginRequest;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    private int $maxAttempts = 5;

    private int $decaySeconds = 1200;

    /**
     * @param  LoginRequest  $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $keyEmail = $credentials['email'].'|'.$request->ip();

        if (\RateLimiter::tooManyAttempts($keyEmail, $this->maxAttempts)) {
            return response()->json([
                'message' => __('auth.throttle', ['seconds' => $this->decaySeconds]),
            ], 429);
        }

        \RateLimiter::hit($keyEmail, $this->decaySeconds);

        $response = AuthManager::handleLogin($credentials);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            \RateLimiter::clear($keyEmail);
        }

        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();
        event(new Logout('api', auth()->user()));

        return response()->json(['message' => __('auth.logout-success')]);
    }

    /**
     * @param  Login2FARequest  $request
     * @return JsonResponse
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws SecretKeyTooShortException
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function loginWith2FA(Login2FARequest $request): JsonResponse
    {
        $validated = $request->validated();

        tenancy()->initialize($validated['tenant']);

        $userId = Crypt::decryptString($validated['temp_token']);
        $user = User::findOrFail($userId);

        if (! Cache::has("2fa_temp_token_{$userId}")) {
            return response()->json(['message' => __('auth.expired-invalid-token')], 401);
        }

        $google2fa = new Google2FA;

        if (! $google2fa->verifyKey($user->two_factor_secret, $validated['otp'])) {
            return response()->json(['message' => __('auth.invalid-otp')], 401);
        }

        Cache::forget("2fa_temp_token_{$userId}");

        if (AuthManager::isPasswordExpired($user)) {
            return AuthManager::shouldRenewPassword($user);
        }

        return AuthManager::login($user);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws JWTException
     */
    public function refreshToken(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = JWTAuth::parseToken()->authenticate();

        $newToken = JWTAuth::refresh();

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'profilPicture' => $user->image,
            'token' => $newToken,
            'viewAdminPanel' => $user->can('view_admin_panel'),
            'viewThemePanel' => $user->can('view_theme_panel'),
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
