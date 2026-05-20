<?php

namespace Tests\Feature\Model;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

#[Group('model')]
class ContactTest extends TenancyTestCase
{
    #[Test]
    public function test_users_relationship()
    {
        $model = new Contact;
        $relation = $model->users();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('contact_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('user_id', $relation->getRelatedPivotKeyName());
        $this->assertEquals('user_has_contact', $relation->getTable());
    }

    #[Test]
    public function test_has_fillable_properties()
    {
        $contact = new Contact;
        $fillable = $contact->getFillable();

        $this->assertContains('last_name', $fillable);
        $this->assertContains('first_name', $fillable);
        $this->assertContains('email', $fillable);
    }

    #[Test]
    public function test_can_soft_delete()
    {
        $contact = Contact::factory()->create();

        $contact->delete();

        $this->assertSoftDeleted('contacts', [
            'id' => $contact->id,
        ]);
    }

    #[Test]
    public function test_can_attach_users()
    {
        $contact = Contact::factory()->create();
        $user = User::factory()->create();

        $contact->users()->attach($user->id);

        $this->assertDatabaseHas('user_has_contact', [
            'contact_id' => $contact->id,
            'user_id' => $user->id,
        ]);
    }
}
