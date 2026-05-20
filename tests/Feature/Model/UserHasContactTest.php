<?php

namespace Tests\Feature\Model;

use App\Models\Contact;
use App\Models\User;
use App\Models\UserHasContact;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class UserHasContactTest extends TenancyTestCase
{
    #[Test]
    public function test_it_user_relationship()
    {
        $pivot = new UserHasContact;
        $relation = $pivot->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals(User::class, get_class($relation->getRelated()));
    }

    #[Test]
    public function test_it_contact_relationship()
    {
        $pivot = new UserHasContact;
        $relation = $pivot->contact();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals(Contact::class, get_class($relation->getRelated()));
    }
}
