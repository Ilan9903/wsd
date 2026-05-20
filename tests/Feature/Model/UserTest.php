<?php

namespace Tests\Feature\Model;

use App\Models\User;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

#[Group('model')]
class UserTest extends TenancyTestCase
{
    use WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'first_name' => 'Bernard',
            'last_name' => 'Gerard',
        ]);
    }

    #[Test]
    public function model_can_be_instantiated(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
    }

    #[Test]
    public function users_can_be_created(): void
    {
        User::create([
            'last_name' => 'Gerard',
            'first_name' => 'Bernard',
            'email' => 'g@b.fr',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'last_name' => 'Gerard',
            'first_name' => 'Bernard',
            'email' => 'g@b.fr',
        ]);
        User::whereEmail('g@b.fr')->delete();
    }

    #[Test]
    public function test_contacts_relationship(): void
    {

        $relation = $this->user->contacts();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('user_id', $relation->getForeignKeyName());
    }

    #[Test]
    public function test_has_links_relationship(): void
    {
        $relation = $this->user->links();

        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('user_id', $relation->getForeignKeyName());
    }

    #[Test]
    public function test_denies_access_to_panel_without_permission(): void
    {

        $panel = $this->createMock(Panel::class);

        $this->assertFalse($this->user->canAccessPanel($panel));
    }

    #[Test]
    public function test_allows_access_to_panel_when_user_has_permission(): void
    {

        $this->user->givePermissionTo('view_users');

        $panel = $this->createMock(Panel::class);

        $this->assertTrue($this->user->canAccessPanel($panel));
    }

    #[Test]
    public function test_returns_full_name_attribute(): void
    {

        $this->assertEquals('Bernard Gerard', $this->user->name);
        $this->assertEquals('Gerard', $this->user->last_name);
        $this->assertEquals('Bernard', $this->user->first_name);

    }

    #[Test]
    public function test_has_clients_relationship(): void
    {

        $relation = $this->user->clients();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('user_has_client', $relation->getTable());

    }

    #[Test]
    public function test_has_group_relationship(): void
    {

        $relation = $this->user->groups();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('user_has_group', $relation->getTable());

    }

    #[Test]
    public function test_returns_jwt_identifier(): void
    {

        $this->assertEquals(
            $this->user->getKey(),
            $this->user->getJWTIdentifier()
        );
    }

    #[Test]
    public function test_returns_jwt_custom_claims(): void
    {

        $claims = $this->user->getJWTCustomClaims();

        $this->assertIsArray($claims);
        $this->assertArrayHasKey('user_id', $claims);
        $this->assertEquals($this->user->getKey(), $claims['user_id']);
    }
}
