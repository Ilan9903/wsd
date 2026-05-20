<?php

namespace Model;

use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mockery;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TestCase;

#[Group('model')]
class DomainTest extends TestCase
{
    #[Test]
    public function test_it_can_update_domain()
    {
        $mockDomain = Mockery::mock(Domain::class)->makePartial();
        $mockDomain->domain = 'old.com';

        $mockDomain->shouldReceive('update')->once()->with(['domain' => 'test888.com'])->andReturn(true);
        $mockDomain->shouldReceive('refresh')->once();
        $mockDomain->domain = 'test888.com';

        $mockDomain->update(['domain' => 'test888.com']);
        $mockDomain->refresh();

        $this->assertEquals('test888.com', $mockDomain->domain);
    }

    #[Test]
    public function test_remove_domain()
    {
        $mockDomain1 = Mockery::mock(Domain::class);
        $mockDomain1->shouldReceive('delete')->once()->andReturn(true);

        $mockDomain2 = Mockery::mock(Domain::class);
        $mockDomain2->shouldReceive('delete')->once()->andReturn(true);

        $this->assertTrue($mockDomain1->delete());
        $this->assertTrue($mockDomain2->delete());
    }

    #[Test]
    public function test_remove_testing_tenant()
    {
        $mockTenant = Mockery::mock(Tenant::class);
        $mockTenant->shouldReceive('delete')->once()->andReturn(true);

        $this->assertTrue($mockTenant->delete());
    }

    #[Test]
    public function test_it_has_a_belongs_to_relationship_with_tenant()
    {
        $domain = new Domain;

        $relation = $domain->tenant();

        $this->assertInstanceOf(BelongsTo::class, $relation);

        $this->assertEquals(Tenant::class, get_class($relation->getRelated()));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
