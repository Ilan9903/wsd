<?php

namespace Services;

use App\Models\Tenant as TenantModel;
use App\Services\Tenant\TenantService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Stancl\Tenancy\Contracts\Domain as DomainContract;
use Tests\Utils\TestCase;

class TenantServiceTest extends TestCase
{
    private string $companyName;

    protected function setUp(): void
    {
        parent::setUp();
        $this->companyName = 'TestCompany';
        config(['wesend.domainApi' => '.example.com']);
    }

    #[Test]
    public function it_creates_a_new_tenant()
    {
        $service = new TenantService;
        $tenant = $service->createTenant($this->companyName);
        $tenantInDB = TenantModel::where('id', '=', 'TestCompany')->first();
        $this->assertEquals($tenantInDB->id, $tenant->id);
        $tenantInDB->forceDelete();
    }

    #[Test]
    public function it_creates_a_domain_for_a_tenant()
    {
        $mockDomain = Mockery::mock(DomainContract::class);

        $mockTenant = Mockery::mock(TenantModel::class);
        $mockTenant->shouldReceive('createDomain')
            ->once()
            ->with([
                'domain' => strtolower($this->companyName).'.example.com',
            ])
            ->andReturn($mockDomain);

        $service = new TenantService;
        $domain = $service->createDomain($this->companyName, $mockTenant);

        $this->assertSame($mockDomain, $domain);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
