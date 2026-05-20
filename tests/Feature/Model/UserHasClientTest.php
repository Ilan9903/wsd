<?php

namespace Model;

use App\Models\Client;
use App\Models\User;
use App\Models\UserHasClient;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TestCase;

#[Group('model')]
class UserHasClientTest extends TestCase
{
    #[Test]
    public function test_it_has_a_belongs_to_relationship_with_user()
    {
        $pivot = new UserHasClient;
        $relation = $pivot->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals(User::class, get_class($relation->getRelated()));
    }

    #[Test]
    public function test_it_has_a_belongs_to_relationship_with_client()
    {
        $pivot = new UserHasClient;
        $relation = $pivot->client();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals(Client::class, get_class($relation->getRelated()));
    }
}
