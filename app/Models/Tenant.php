<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * @mixin IdeHelperTenant
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains ,HasFactory;

    /**
     * @return BelongsToMany<Client, Tenant, ClientHasTenant>
     */
    public function clients(): BelongsToMany
    {
        /** @var BelongsToMany<Client, Tenant, ClientHasTenant> */
        return $this->belongsToMany(
            Client::class,
            'client_has_tenant',
            'tenant_id',
            'global_client_id',
            'id',
            'global_id'
        )->using(ClientHasTenant::class);
    }
}
