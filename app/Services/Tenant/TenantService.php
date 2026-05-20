<?php

namespace App\Services\Tenant;

use App\Models\Tenant;
use Stancl\Tenancy\Contracts\Domain;

class TenantService
{
    /**
     * @param  string  $companyName
     * @return Tenant
     */
    public function createTenant(string $companyName): Tenant
    {
        return Tenant::create([
            'id' => $companyName,
            'data' => [
                'company_name' => $companyName,
            ],
        ]);
    }

    /**
     * Crée le domaine du tenant.
     */
    public function createDomain(string $companyName, Tenant $tenant): Domain
    {
        return $tenant->createDomain([
            'domain' => strtolower($companyName).config('wesend.domainApi'),

        ]);
    }
}
