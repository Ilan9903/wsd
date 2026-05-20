<?php

namespace Hopla\AuthManagement\Handlers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthManager
{
    public function __construct(
        protected CredentialsValidator $validator,
        protected TwoFactorHandler $twoFactor,
        protected PasswordRenewalHandler $passwordRenewal,
        protected UserLoginHandler $userLogin
    ) {}

    /**
     * @param  array<string>  $credentials
     * @return JsonResponse
     */
    public function handleLogin(array $credentials): JsonResponse
    {
        $user = User::withTrashed()->where('email', $credentials['email'])->first();

        if (! $this->validator->isValid($user, $credentials['password'])) {
            return response()->json(['message' => __('auth.invalid-credentials.')], 401);
        }

        if ($user->trashed()) {
            return response()->json(['message' => __('auth.user-deactivated')], 401);
        }

        if ($this->validator->is2FAEnabled($user)) {
            return $this->twoFactor->loginWith2FA($user);
        }

        if ($this->validator->is2FARequired($user)) {
            return $this->twoFactor->askEnable2FA($user);
        }

        if ($this->validator->isPasswordExpired($user)) {
            return $this->passwordRenewal->renewPassword($user);
        }

        return $this->userLogin->login($user);
    }

    public function isPasswordExpired(User $user): bool
    {
        return $this->validator->isPasswordExpired($user);
    }

    public function shouldRenewPassword(User $user): JsonResponse
    {
        return $this->passwordRenewal->renewPassword($user);
    }

    public function login(User $user): JsonResponse
    {
        return $this->userLogin->login($user);
    }
}
