<?php

namespace Hopla\AuthManagement\Handlers;

use App\Models\TenantConfig;
use App\Models\TenantContract;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CredentialsValidator
{
    public function isValid(?User $user, string $password): bool
    {
        return $user && Hash::check($password, $user->password);
    }

    public function is2FAEnabled(User $user): bool
    {
        return (bool) $user->two_factor_verified_at;
    }

    public function is2FARequired(User $user): bool
    {
        return ! $user->two_factor_verified_at && TenantContract::value('is_health');
    }

    public function isPasswordExpired(User $user): bool
    {
        $config = TenantConfig::firstOrCreate();

        if (! $config->isPasswordShouldRenew) {
            return false;
        }

        $lastUpdate = new \DateTime($user->password_last_update_at);

        return now()->diff($lastUpdate)->days > $config->passwordExpirationDelay;
    }
}
