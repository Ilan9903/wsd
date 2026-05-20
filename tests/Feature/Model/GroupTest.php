<?php

namespace Tests\Feature\Model;

use App\Models\Group;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

#[Group('tenant')]
#[Group('model')]
class GroupTest extends TenancyTestCase
{
    private User $user;

    private Group $group;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->group = Group::factory()->create([
            'owner_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function it_has_expected_fillable_fields()
    {
        $group = Group::factory()->create([
            'name' => 'Hopla',
            'owner_id' => $this->user->id,
        ]);

        $this->assertEquals([
            'id',
            'name',
            'owner_id',
            'created_at',
        ], $group->getFillable());
    }

    #[Test]
    public function it_has_users_relation()
    {

        $this->group->users()->save($this->user);
        $this->assertEquals($this->user->id, $this->group->users->first()->id);
    }

    #[Test]
    public function it_has_owner_relation()
    {
        $group = Group::factory()->create([
            'name' => 'Hopla2359',
            'owner_id' => $this->user->id,
        ]);
        $this->assertEquals($group->owner_id, $this->user->id);
        $group->delete();
    }

    protected function tearDown(): void
    {
        $this->user->delete();
        $this->group->delete();
        parent::tearDown();
    }
}
