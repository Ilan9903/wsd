<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperClientHasTenant
 */
class ClientHasTenant extends Pivot
{
    protected $table = 'client_has_tenant';

    public $incrementing = true;

    protected $fillable = [
        'tenant_id',
        'global_client_id',
    ];

    /**
     * @return BelongsTo<Client, Tenant>
     */
    public function client(): BelongsTo
    {
        /** @var BelongsTo<Client, Tenant> */
        return $this->belongsTo(Client::class, 'tenant_id');
    }

    /**
     * @return BelongsTo<Tenant, Client>
     */
    public function tenant(): BelongsTo
    {
        /** @var BelongsTo<Tenant, Client> */
        return $this->belongsTo(Tenant::class, 'global_client_id');
    }
}
