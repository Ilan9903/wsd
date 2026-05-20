<?php

namespace Tests\Feature\Model;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TestCase;

class TenantTest extends TestCase
{
    #[Test]
    public function it_has_clients_relationship(): void
    {
        $tenant = new Tenant;
        $relation = $tenant->clients();
        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('client_has_tenant', $relation->getTable());
        $this->assertEquals('tenant_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('global_client_id', $relation->getRelatedPivotKeyName());
        $this->assertEquals('id', $relation->getParentKeyName());
        $this->assertEquals('global_id', $relation->getRelatedKeyName());
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
