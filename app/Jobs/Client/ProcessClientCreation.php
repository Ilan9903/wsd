<?php

namespace App\Jobs\Client;

use App\Models\Client;
use App\Models\TenantConfig;
use App\Models\TenantContract;
use App\Notifications\ClientCreatedNotification;
use App\Services\Tenant\TenantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessClientCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    private Client $client;

    private string $tenantName;

    /**
     * Create a new job instance.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->tenantName = strtolower(preg_replace('/[^a-zA-Z]+/', '', $client->name ?? ''));
    }

    /**
     * Execute the job.
     */
    public function handle(TenantService $tenantService): void
    {
        $url = $this->tenantName.config('wesend.domain');

        $tenantInstance = $tenantService->createTenant($this->tenantName);
        $tenantService->createDomain($this->tenantName, $tenantInstance);

        $this->client->update([
            'tenant' => $this->tenantName,
            'domain' => $this->tenantName.config('wesend.domainApi'),
            'bucket' => $this->tenantName,
            'front_route' => $url,
        ]);

        $this->client->tenants()->attach($tenantInstance);
        tenancy()->initialize($this->tenantName);
        TenantContract::create([
            'global_id' => $this->client->global_id,
            'used_storage' => $this->client->used_storage,
            'allocated_storage' => $this->client->allocated_storage,
            'allocated_users' => $this->client->allocated_users,
            'bucket' => $this->client->bucket,
            'is_health' => $this->client->is_health,
        ]);
        TenantConfig::create([
            'isPasswordShouldRenew' => false,
            'passwordExpirationDelay' => 45,
        ]);
        tenancy()->end();
        $this->client->notify(new ClientCreatedNotification($this->client));
    }
}
