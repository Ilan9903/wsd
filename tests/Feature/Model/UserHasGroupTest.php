<?php

namespace Tests\Feature\Model;

use App\Models\Group;
use App\Models\User;
use App\Models\UserHasGroup;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class UserHasGroupTest extends TenancyTestCase
{
    #[Test]
    public function test_it_user_relationship()
    {
        $pivot = new UserHasGroup;
        $relation = $pivot->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals(User::class, get_class($relation->getRelated()));
    }

    #[Test]
    public function test_it_group_relationship()
    {
        $pivot = new UserHasGroup;
        $relation = $pivot->group();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals(Group::class, get_class($relation->getRelated()));
    }
}
