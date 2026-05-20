<?php

namespace Tests\Unit\Hopla\FileOrFolderManagement\Handlers;

use App\Enums\FileOrFolderType;
use App\Models\FileOrFolder;
use App\Models\User;
use Hopla\FileOrFolderManagement\DTO\UploadedFile;
use Hopla\FileOrFolderManagement\Handlers\FolderHierarchy;
use Illuminate\Support\Facades\Cache;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Utils\TenancyTestCase;

class FolderHierarchyTest extends TenancyTestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

    }

    private function mockUploadedFile(array $metadata): UploadedFile
    {
        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getInfo')->andReturn([
            'metadata' => $metadata,
        ]);

        return $file;
    }

    #[Test]
    public function test_it_returns_upload_base_folder_when_no_path_parts()
    {
        $file = $this->mockUploadedFile(['relativePath' => 'file.txt']);
        $hierarchy = new FolderHierarchy;

        $baseFolder = FileOrFolder::factory()->create();

        $result = $hierarchy->createFolderHierarchy($file, $baseFolder);

        $this->assertEquals($baseFolder->id, $result->id);
    }

    #[Test]
    public function test_it_creates_folder_hierarchy()
    {
        auth()->login($this->user);

        $file = $this->mockUploadedFile(['relativePath' => 'folderA/folderB/file.txt']);

        $hierarchy = new FolderHierarchy;

        $baseFolder = FileOrFolder::factory()->folder()->forUser($this->user)->create();

        $result = $hierarchy->createFolderHierarchy($file, $baseFolder);

        $this->assertDatabaseHas('file_or_folders', [
            'name' => 'folderA',
            'parent_id' => $baseFolder->id,
            'type' => FileOrFolderType::Folder,
        ]);

        $folderA = FileOrFolder::where('name', 'folderA')->first();

        $this->assertDatabaseHas('file_or_folders', [
            'name' => 'folderB',
            'parent_id' => $folderA->id,
            'type' => FileOrFolderType::Folder,
        ]);

        $this->assertEquals('folderB', $result->name);
    }

    #[Test]
    public function test_it_throws_conflict_if_folder_is_soft_deleted()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('conflict');

        auth()->login($this->user);

        $baseFolder = FileOrFolder::factory()->folder()->forUser($this->user)->create();

        FileOrFolder::factory()->folder()->forUser($this->user)->create([
            'name' => 'folderA',
            'parent_id' => $baseFolder->id,
            'type' => FileOrFolderType::Folder,
            'deleted_at' => now(),
        ]);

        $file = $this->mockUploadedFile(['relativePath' => 'folderA/file.txt']);

        $hierarchy = new FolderHierarchy;

        $hierarchy->createFolderHierarchy($file, $baseFolder);
    }

    #[Test]
    public function test_it_reuses_existing_folders()
    {
        auth()->login($this->user);

        $baseFolder = FileOrFolder::factory()->folder()->forUser($this->user)->create();

        $existingA = FileOrFolder::factory()->create([
            'name' => 'folderA',
            'parent_id' => $baseFolder->id,
            'type' => FileOrFolderType::Folder,
        ]);

        $file = $this->mockUploadedFile(['relativePath' => 'folderA/folderB/file.txt']);

        $hierarchy = new FolderHierarchy;

        $result = $hierarchy->createFolderHierarchy($file, $baseFolder);

        $this->assertEquals($existingA->id, $existingA->fresh()->id);

        $this->assertDatabaseHas('file_or_folders', [
            'name' => 'folderB',
            'parent_id' => $existingA->id,
        ]);

        $this->assertEquals('folderB', $result->name);
    }

    #[Test]
    public function test_it_uses_laravel_lock_to_prevent_race_conditions()
    {
        auth()->login($this->user);

        $file = $this->mockUploadedFile(['relativePath' => 'folderA/file.txt']);

        $baseFolder = FileOrFolder::factory()->folder()->forUser($this->user)->create();

        $lock = Mockery::mock();
        $lock->shouldReceive('block')
            ->once()
            ->andReturnUsing(function ($time, $callback) {
                $callback();
            });

        Cache::shouldReceive('lock')
            ->once()
            ->andReturn($lock);

        $hierarchy = new FolderHierarchy;
        $hierarchy->createFolderHierarchy($file, $baseFolder);

        $this->assertDatabaseHas('file_or_folders', [
            'name' => 'folderA',
            'parent_id' => $baseFolder->id,
        ]);
    }
}
