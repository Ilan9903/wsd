<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lomkit\Access\Controls\HasControl;
use Stancl\Tenancy\Contracts\Syncable;
use Stancl\Tenancy\Database\Concerns\ResourceSyncing;

/**
 * @mixin IdeHelperTenantContract
 */
class TenantContract extends Model implements Syncable
{
    use HasControl, ResourceSyncing;

    protected $fillable = [
        'global_id',
        'used_storage',
        'allocated_storage',
        'allocated_users',
        'bucket',
        'is_health',
        'wedrop_url',
    ];

    /**
     * @return string
     */
    public function getGlobalIdentifierKeyName(): string
    {
        return 'global_id';
    }

    /**
     * @return mixed
     */
    public function getGlobalIdentifierKey(): mixed
    {
        return $this->getAttribute($this->getGlobalIdentifierKeyName());
    }

    /**
     * @return string
     */
    public function getCentralModelName(): string
    {
        return Client::class;
    }

    /**
     * @return string[]
     */
    public function getSyncedAttributeNames(): array
    {
        return [
            'used_storage',
            'allocated_storage',
            'allocated_users',
            'bucket',
            'wedrop_url',
        ];
    }
}
