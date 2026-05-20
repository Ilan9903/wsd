<?php

namespace App\Services\Tenant;

use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Stancl\Tenancy\Facades\Tenancy;

class TenantFilament
{
    /**
     * @return bool
     */
    public static function availableForNavigation(): bool
    {
        $tenantName = explode('.', request()->server('HTTP_X_FORWARDED_HOST'))[0];
        $tenant = Tenancy::find($tenantName);
        if (! $tenant) {
            return false;
        }

        return true;
    }

    /**
     * @return void
     *
     * @throws TenantCouldNotBeIdentifiedById
     */
    public static function initializeTenant(): void
    {
        $name = explode('.', request()->server('HTTP_X_FORWARDED_HOST'))[0];
        $tenant = Tenancy::find($name);

        if ($tenant) {
            Tenancy::initialize($tenant);

        }
    }
}
