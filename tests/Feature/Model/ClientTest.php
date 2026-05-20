<?php

namespace Model;

use App\Models\Client;
use App\Models\ClientHasTenant;
use App\Models\Tenant;
use App\Models\TenantContract;
use App\Models\User;
use App\Models\UserHasClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TestCase;

#[Group('model')]
class ClientTest extends TestCase
{
    private User $user;

    private string $name;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->name = $this->user->firstname.'_'.$this->user->lastname;
        User::unsetEventDispatcher();
    }

    #[Test]
    public function it_has_expected_fillable_fields()
    {
        $version = new Client;

        $this->assertEquals([
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
        ], $version->getFillable());
    }

    #[Test]
    public function it_has_users_relationship()
    {
        $relation = Mockery::mock(BelongsToMany::class);
        $relation->shouldReceive('getResults')->andReturn(collect([
            Mockery::mock(User::class),
            Mockery::mock(User::class),
        ]));

        $client = Mockery::mock(Client::class)->makePartial();
        $client->shouldReceive('users')->andReturn($relation);

        $this->assertInstanceOf(Collection::class, $client->users);
        $this->assertCount(2, $client->users);
    }

    #[Test]
    public function it_has_users_belongs_to_many_relationship()
    {
        $client = new Client;
        $relation = $client->users();

        $this->assertInstanceOf(BelongsToMany::class, $relation);

        $this->assertEquals('user_has_client', $relation->getTable());
        $this->assertEquals(User::class, get_class($relation->getRelated()));
        $this->assertEquals(UserHasClient::class, $relation->getPivotClass());
    }

    #[Test]
    public function it_has_a_tenants_belongs_to_many_relationship()
    {
        $client = new Client;
        $relation = $client->tenants();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('client_has_tenant', $relation->getTable());
        $this->assertEquals('global_client_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('tenant_id', $relation->getRelatedPivotKeyName());
        $this->assertEquals('global_id', $relation->getParentKeyName());
        $this->assertEquals(Tenant::class, get_class($relation->getRelated()));
        $this->assertEquals(ClientHasTenant::class, $relation->getPivotClass());
    }

    #[Test]
    public function it_returns_synced_attributes()
    {
        $client = new Client;

        $attributes = [
            'used_storage',
            'allocated_storage',
            'allocated_users',
            'bucket',
            'wedrop_url',
        ];

        $this->assertEquals($attributes, $client->getSyncedAttributeNames());
    }

    #[Test]
    public function it_returns_correct_global_identifier()
    {
        $client = Mockery::mock(Client::class)->makePartial();

        $client->shouldReceive('getGlobalIdentifierKeyName')->andReturn('global_id');
        $client->shouldReceive('getAttribute')->with('global_id')->andReturn('fake_global_id');

        $this->assertEquals('fake_global_id', $client->getGlobalIdentifierKey());
    }

    #[Test]
    public function it_returns_correct_tenant_model_name()
    {
        $client = new Client;

        $this->assertEquals(TenantContract::class, $client->getTenantModelName());
    }

    #[Test]
    public function it_returns_correct_global_identifier_key_name()
    {
        $client = new Client;

        $this->assertEquals('global_id', $client->getGlobalIdentifierKeyName());
    }

    #[Test]
    public function it_returns_its_own_class_name_as_central_model_name()
    {
        $client = new Client;

        $this->assertEquals(Client::class, $client->getCentralModelName());
    }

    #[Test]
    public function it_builds_a_prunable_query()
    {
        $client = new Client;

        $query = $client->prunable();

        $this->assertInstanceOf(Builder::class, $query);
        $this->assertStringContainsString(
            'select * from',
            $query->toSql()
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        $this->user->delete();
        parent::tearDown();

    }
}
