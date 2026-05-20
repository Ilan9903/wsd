<?php

namespace Tests\Unit\Hopla\FileOrFolderManagement\Listeners;

use App\Enums\FileOrFolderType;
use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\Handlers\FileStorageManager;
use Hopla\FileOrFolderManagement\Listeners\HandleFileCreated;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TestCase;

class HandleFileCreatedTest extends TestCase
{
    private FileStorageManager $fileStorageManager;

    private HandleFileCreated $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileStorageManager = Mockery::mock(FileStorageManager::class);
        $this->listener = new HandleFileCreated($this->fileStorageManager);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function test_it_adds_file_to_storage_when_type_is_file(): void
    {
        $fileOrFolder = Mockery::mock(FileOrFolder::class)->makePartial();
        $fileOrFolder->type = FileOrFolderType::File;

        $this->fileStorageManager
            ->shouldReceive('addToStorage')
            ->once()
            ->with($fileOrFolder);

        $this->listener->handle($fileOrFolder);

        $this->assertTrue(true);
    }

    #[Test]
    public function test_it_does_not_add_to_storage_when_type_is_folder(): void
    {
        $fileOrFolder = Mockery::mock(FileOrFolder::class)->makePartial();
        $fileOrFolder->type = FileOrFolderType::Folder;

        $this->fileStorageManager
            ->shouldReceive('addToStorage')
            ->never();

        $this->listener->handle($fileOrFolder);

        $this->assertTrue(true);
    }

    #[Test]
    public function test_it_can_be_instantiated_with_file_storage_manager(): void
    {
        $listener = new HandleFileCreated($this->fileStorageManager);

        $this->assertInstanceOf(HandleFileCreated::class, $listener);
    }

    #[Test]
    public function test_it_handles_multiple_file_creations_sequentially(): void
    {
        $file1 = Mockery::mock(FileOrFolder::class)->makePartial();
        $file1->type = FileOrFolderType::File;

        $file2 = Mockery::mock(FileOrFolder::class)->makePartial();
        $file2->type = FileOrFolderType::File;

        $this->fileStorageManager
            ->shouldReceive('addToStorage')
            ->twice()
            ->withArgs(function ($arg) use ($file1, $file2) {
                return $arg === $file1 || $arg === $file2;
            });

        $this->listener->handle($file1);
        $this->listener->handle($file2);

        $this->assertTrue(true);
    }

    #[Test]
    public function test_it_only_processes_files_and_ignores_folders_in_mixed_batch(): void
    {
        $file = Mockery::mock(FileOrFolder::class)->makePartial();
        $file->type = FileOrFolderType::File;

        $folder = Mockery::mock(FileOrFolder::class)->makePartial();
        $folder->type = FileOrFolderType::Folder;

        $this->fileStorageManager
            ->shouldReceive('addToStorage')
            ->once()
            ->with($file);

        $this->listener->handle($file);
        $this->listener->handle($folder);

        $this->assertTrue(true);
    }
}
