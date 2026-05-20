<?php

namespace Tests\Unit\Hopla\FileOrFolderManagement\Listeners;

use App\Enums\FileOrFolderType;
use App\Models\FileOrFolder;
use App\Models\User;
use Hopla\FileOrFolderManagement\Handlers\FileStorageManager;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class FileStorageManagerTest extends TenancyTestCase
{
    #[Test]
    public function test_add_to_storage()
    {
        \Event::fake();

        $user = User::factory()->create();

        $fileOrFolder = FileOrFolder::factory()->make([
            'user_id' => $user->id,
            'type' => FileOrFolderType::File,
            'size' => 1000,
        ]);

        $oldUsedStorage = $user->used_storage;

        (new FileStorageManager)->addToStorage($fileOrFolder);
        $user->refresh();

        $this->assertEquals($oldUsedStorage + $fileOrFolder->size, $user->used_storage);
    }

    #[Test]
    public function test_remove_from_storage()
    {
        \Event::fake();

        $user = User::factory()->create([
            'used_storage' => 1000,
        ]);

        $fileOrFolder = FileOrFolder::factory()->make([
            'user_id' => $user->id,
            'type' => FileOrFolderType::File,
            'size' => 1000,
        ]);

        $oldUsedStorage = $user->used_storage;

        (new FileStorageManager)->removeToStorage($fileOrFolder);
        $user->refresh();

        $this->assertEquals($oldUsedStorage - $fileOrFolder->size, $user->used_storage);
    }
}
