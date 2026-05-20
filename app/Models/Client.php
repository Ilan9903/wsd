<?php

namespace App\Models;

use App\Services\Minio\Minio;
use Database\Factories\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Lomkit\Access\Controls\HasControl;
use Stancl\Tenancy\Contracts\SyncMaster;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Concerns\ResourceSyncing;

/**
 * @mixin IdeHelperClient
 */
class Client extends Model implements SyncMaster
{
    /** @use HasFactory<ClientFactory> */
    use CentralConnection, HasControl,HasFactory, HasUuids, Notifiable, Prunable, ResourceSyncing, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'zipcode',
        'avatar',
        'tenant',
        'domain',
        'bucket',
        'front_route',
        'allocated_users',
        'allocated_storage',
        'used_storage',
        'is_health',
        'wedrop_url',
    ];

    protected $hidden = [
    ];

    /**
     * @return BelongsToMany<Client,User>
     */
    public function users(): BelongsToMany
    {
        /** @var BelongsToMany<Client,User> */
        return $this->belongsToMany(User::class, 'user_has_client')->using(UserHasClient::class);
    }

    /**
     * @return BelongsToMany<Tenant, Client, ClientHasTenant>
     */
    public function tenants(): BelongsToMany
    {
        /** @var BelongsToMany<Tenant, Client, ClientHasTenant> */
        return $this->belongsToMany(
            Tenant::class,
            'client_has_tenant',
            'global_client_id',
            'tenant_id',
            'global_id'
        )->using(ClientHasTenant::class);
    }

    /**
     * @return string
     */
    public function getTenantModelName(): string
    {
        return TenantContract::class;
    }

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
        return static::class;
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

    /**
     * @return Builder|Client
     */
    public function prunable(): Builder|Client
    {
        return static::where('deleted_at', '<', now()->subDays(30));
    }

    /**
     * @return void
     *
     * @throws GuzzleException
     */
    public function pruning(): void
    {
        $tenants = $this->tenants()->get();

        foreach ($tenants as $tenant) {
            $this->tenants()->detach($tenant->id);
            foreach ($tenant->domains as $domain) {
                $domain->delete();
            }
            $tenant->delete();
        }
        app(Minio::class)->removeBucket($this->bucket);
    }
}
