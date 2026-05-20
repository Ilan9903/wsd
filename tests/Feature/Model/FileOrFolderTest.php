<?php

namespace Model;

use App\Models\FileOrFolder;
use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PHPUnit\Framework\Attributes\Group as AtributeGroup;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\Datasets\Test\EmptyDataSet;
use Tests\Utils\TenancyTestCase;

#[AtributeGroup('file')]
#[AtributeGroup('model')]
class FileOrFolderTest extends TenancyTestCase
{
    private User $user;

    private FileOrFolder $folder;

    private FileOrFolder $folder2;

    private Link $link;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->link = Link::factory()->create([
            'user_id' => $this->user->id,
            'expired_at' => now()->addDay(),
        ]);
        $this->folder = FileOrFolder::factory()
            ->forUser($this->user)
            ->folder()->create([
                'name' => 'to delelte',
                'link_id' => $this->link->id,
            ]);
        $this->folder2 = FileOrFolder::factory()
            ->forUser($this->user)
            ->folder()
            ->create([
                'name' => 'to2 delelte',
                'parent_id' => $this->folder->id,
                'link_id' => $this->link->id,
            ]);
    }

    protected function tearDown(): void
    {
        $this->user->delete();
        $this->folder->delete();
        parent::tearDown();

    }

    #[Test]
    public function test_it_can_create_a_file_or_folders(): void
    {
        FileOrFolder::factory()->forUser($this->user)->create([
            'name' => 'test amigoooo', ]);

        $this->assertDatabaseHas('file_or_folders', [
            'name' => 'test amigoooo',
        ]);
    }

    #[Test]
    public function test_it_can_update_a_file_or_folders(): void
    {
        $this->folder->update(['name' => 'test_amig']);
        $this->folder->refresh();
        $this->assertEquals('test_amig', $this->folder->name);
    }

    #[Test]
    public function test_it_can_soft_delete_a_file_or_folder(): void
    {
        $this->folder->delete();
        $this->assertNotNull(FileOrFolder::withTrashed()->find($this->folder->id));
        $this->assertNotNull($this->folder->deleted_at);
    }

    #[Test]
    public function test_it_can_force_delete_a_file_or_folder(): void
    {

        $this->folder2->forceDelete();

        $this->assertNull(FileOrFolder::find($this->folder2->id));
    }

    #[Test]
    public function test_it_has_one_link_relation(): void
    {
        $relation = $this->folder->link();

        $this->assertInstanceOf(HasOne::class, $relation);
        $this->assertEquals('link_id', $relation->getLocalKeyName());
        $this->assertEquals('id', $relation->getForeignKeyName());
    }

    #[Test]
    public function test_it_can_retrieve_link(): void
    {
        $this->assertNotNull($this->folder->link);
        $this->assertEquals($this->link->id, $this->folder->link->id);
    }

    #[Test]
    public function test_it_has_many_childrens(): void
    {
        $relation = $this->folder->childrens();
        $this->assertInstanceOf(HasMany::class, $relation);
        $this->assertEquals('parent_id', $relation->getForeignKeyName());
    }

    #[Test]
    public function test_it_can_retrieve_childrens(): void
    {
        $children = $this->folder->childrens;
        $this->assertCount(1, $children);
        $this->assertTrue($children->first()->is($this->folder2));
    }

    #[Test]
    public function test_belongs_to_user(): void
    {
        $relation = $this->folder->user();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('user_id', $relation->getForeignKeyName());
    }

    #[Test]
    public function test_it_can_retrieve_users(): void
    {
        $user = $this->folder->user;
        $this->assertTrue($user->is($this->user));
    }

    #[Test]
    public function test_belongs_to_parent(): void
    {
        $relation = $this->folder2->parent();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('parent_id', $relation->getForeignKeyName());
    }

    #[Test]
    public function test_it_can_retrieve_parent(): void
    {
        $parent = $this->folder2->parent;
        $this->assertTrue($parent->is($this->folder));
    }

    protected function getDatasetClass(): string
    {
        return EmptyDataSet::class;
    }
}
