<?php

namespace Tests\Feature\Jobs;

use App\Jobs\Client\ProcessClientCreation;
use App\Models\Client;
use App\Models\Tenant;
use App\Notifications\ClientCreatedNotification;
use App\Services\Tenant\TenantService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TestCase;

class ProcessClientCreationTest extends TestCase
{
    #[Test]
    public function test_it_creates_tenant_and_updates_client_and_sends_notification(): void
    {
        Notification::fake();

        $name = faker()->name().'test'.rand(0, 500);
        $tenantName = strtolower(preg_replace('/[^a-zA-Z]+/', '', $name));

        $client = Client::factory()->create([
            'name' => $tenantName,
        ]);

        $tenantInstance = Tenant::factory()->create(['id' => Str::uuid()]);

        $tenantService = Mockery::mock(TenantService::class);
        $tenantService
            ->shouldReceive('createTenant')
            ->once()
            ->with($tenantName)
            ->andReturn($tenantInstance);

        $tenantService
            ->shouldReceive('createDomain')
            ->once()
            ->with($tenantName, $tenantInstance);

        $this->app->instance(TenantService::class, $tenantService);

        (new ProcessClientCreation($client))->handle($tenantService);

        $client->refresh();

        $this->assertEquals($tenantName, $client->tenant);
        $this->assertEquals($tenantName.config('wesend.domainApi'), $client->domain);
        $this->assertEquals($tenantName, $client->bucket);

        Notification::assertSentTo(
            $client,
            ClientCreatedNotification::class
        );

        $tenantInstance->clients()->detach($client);
        $tenantInstance->delete();
        Artisan::call('model:prune');
    }
}
