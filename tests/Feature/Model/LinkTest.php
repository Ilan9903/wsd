<?php

namespace Tests\Feature\Model;

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

#[Group('model')]
class LinkTest extends TenancyTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_recognizes_valid_link()
    {
        $link = Link::factory()->create([
            'user_id' => $this->user->id,
            'expired_at' => now()->addDays(15),
        ]);
        $this->assertTrue($link->isValid());
    }

    #[Test]
    public function it_detects_expired_link()
    {
        $link = Link::factory()->create([
            'user_id' => $this->user->id,
            'expired_at' => now()->subDays(31),
        ]);
        $this->assertFalse($link->isValid());
    }

    #[Test]
    public function it_detects_password_protection()
    {
        $linkWithoutPassword = Link::factory()->withoutPassword()->create([
            'user_id' => $this->user->id,
            'expired_at' => now()->addDays(31),
        ]);
        $linkWithPassword = Link::factory()->create([
            'password' => Hash::make('test'),
            'user_id' => $this->user->id,
            'expired_at' => now()->addDays(31),
        ]);
        $this->assertTrue($linkWithPassword->hasPassword());
        $this->assertFalse($linkWithoutPassword->hasPassword());
    }

    #[Test]
    public function it_checks_password_correctly()
    {
        $password = 'testPassword';
        $link = Link::factory()->create([
            'password' => Hash::make($password),
            'user_id' => $this->user->id,
            'expired_at' => now()->addDays(31),
        ]);

        $this->assertTrue($link->checkPassword('testPassword'));
        $this->assertFalse($link->checkPassword('wrongPassword'));
    }

    #[Test]
    public function it_user_relationship()
    {
        $model = new Link;

        $relation = $model->user();

        $this->assertInstanceOf(HasOne::class, $relation);
        $this->assertEquals('id', $relation->getForeignKeyName());
    }
}
