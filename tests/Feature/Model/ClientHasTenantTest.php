<?php

namespace Tests\Feature\Model;

use App\Models\Client;
use App\Models\ClientHasTenant;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class ClientHasTenantTest extends TenancyTestCase
{
    #[Test]
    public function it_has_tenant_relationship()
    {
        $pivotTable = new ClientHasTenant;
        $relation = $pivotTable->tenant();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals(Tenant::class, get_class($relation->getRelated()));
    }

    #[Test]
    public function it_has_client_relationship()
    {
        $pivotTable = new ClientHasTenant;
        $relation = $pivotTable->client();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals(Client::class, get_class($relation->getRelated()));
    }
}
