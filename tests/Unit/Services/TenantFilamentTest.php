<?php

namespace Tests\Unit\Services;

use App\Services\Tenant\TenantFilament;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\Utils\TestCase;

class TenantFilamentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $request = Request::create('/', 'GET');
        $request->server->set('HTTP_X_FORWARDED_HOST', 'xefi.api.wesend.xyz');

        $this->app->instance('request', $request);
    }

    #[Test]
    public function it_returns_true_when_tenant_exists_for_navigation(): void
    {
        Tenancy::shouldReceive('find')
            ->once()
            ->with('xefi')
            ->andReturn($this->mockTenant());

        $this->assertTrue(TenantFilament::availableForNavigation());
    }

    #[Test]
    public function it_returns_false_when_tenant_does_not_exist_for_navigation(): void
    {
        Tenancy::shouldReceive('find')
            ->once()
            ->with('xefi')
            ->andReturn(null);

        $this->assertFalse(TenantFilament::availableForNavigation());
    }

    #[Test]
    public function it_initializes_tenant_when_tenant_exists(): void
    {
        $tenant = $this->mockTenant();

        Tenancy::shouldReceive('find')
            ->once()
            ->with('xefi')
            ->andReturn($tenant);

        Tenancy::shouldReceive('initialize')
            ->once()
            ->with($tenant);

        TenantFilament::initializeTenant();
    }

    #[Test]
    public function it_does_not_initialize_tenant_when_tenant_does_not_exist(): void
    {
        Tenancy::shouldReceive('find')
            ->once()
            ->with('xefi')
            ->andReturn(null);

        Tenancy::shouldReceive('initialize')
            ->never();

        TenantFilament::initializeTenant();
    }

    private function mockTenant(): Tenant
    {
        return mock(Tenant::class);
    }
}
