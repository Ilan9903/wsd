<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

/**
 * @mixin IdeHelperDomain
 */
class Domain extends BaseDomain
{
    protected $fillable = [
        'domain',
        'front_route',
        'tenant_id',
    ];

    /**
     * @return BelongsTo<Tenant, Domain>
     */
    public function tenant(): BelongsTo
    {
        /** @var BelongsTo<Tenant, Domain> $relation */
        $relation = $this->belongsTo(Tenant::class);

        return $relation;
    }
}
