<?php

namespace Tests\Unit\Hopla\FileOrFolderManagement\Handlers;

use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\DTO\UploadedFile;
use Hopla\FileOrFolderManagement\Handlers\PathManager;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class PathManagerTest extends TenancyTestCase
{
    #[Test]
    public function test_it_returns_null_when_metadata_parent_is_string_null()
    {
        $file = $this->mock(UploadedFile::class);
        $file->shouldReceive('getInfo')->andReturn([
            'metadata' => [
                'parentId' => 'null',
            ],
        ]);

        $manager = new PathManager;

        $this->assertNull($manager->getUploadBaseFolder($file));
    }

    #[Test]
    public function test_it_returns_the_parent_folder_when_parent_exists()
    {
        $folder = FileOrFolder::factory()->create();

        $file = $this->mock(UploadedFile::class);
        $file->shouldReceive('getInfo')->andReturn([
            'metadata' => [
                'parentId' => $folder->id,
            ],
        ]);

        $manager = new PathManager;

        $result = $manager->getUploadBaseFolder($file);

        $this->assertNotNull($result);
        $this->assertEquals($folder->id, $result->id);
    }

    #[Test]
    public function test_it_returns_null_if_parent_does_not_exist()
    {
        $file = $this->mock(UploadedFile::class);
        $file->shouldReceive('getInfo')->andReturn([
            'metadata' => [
                'parentId' => 'non-existent-uuid',
            ],
        ]);

        $manager = new PathManager;

        $this->assertNull($manager->getUploadBaseFolder($file));
    }

    #[Test]
    public function test_it_returns_path_parts_from_a_relative_path()
    {
        $parts = PathManager::getPathParts('folder/subfolder/file.txt');

        $this->assertEquals(['folder', 'subfolder'], $parts);
    }

    #[Test]
    public function test_it_ignores_trailing_slash()
    {
        $parts = PathManager::getPathParts('folder/subfolder/file.txt/');

        $this->assertEquals(['folder', 'subfolder', 'file.txt'], $parts);
    }

    #[Test]
    public function test_it_handles_single_level_paths()
    {
        $parts = PathManager::getPathParts('file.txt');

        $this->assertEquals([], $parts);
    }
}
