<?php

namespace Tests\Unit\Hopla\FileOrFolderManagement\Handlers;

use App\Enums\FileOrFolderType;
use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\Handlers\PathBuilder;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class PathBuilderTest extends TenancyTestCase
{
    #[Test]
    public function test_it_builds_path_for_root_folder()
    {
        $path = PathBuilder::build(null);

        $this->assertEquals('/', $path);
    }

    #[Test]
    public function test_it_builds_minio_path_for_child()
    {
        $folder = FileOrFolder::factory()->folder()->create();
        $file = FileOrFolder::factory()->create([
            'parent_id' => $folder->id,
            'type' => FileOrFolderType::File,
        ]);

        $path = PathBuilder::buildMinioPath($file);

        $this->assertEquals($folder->id.'/', $path);
    }

    #[Test]
    public function test_it_builds_minio_path_for_root()
    {
        $file = FileOrFolder::factory()->create([
            'parent_id' => null,
        ]);

        $path = PathBuilder::buildMinioPath($file);

        $this->assertEquals('/', $path);
    }

    #[Test]
    public function test_it_builds_path_for_child_folder()
    {
        $parent = FileOrFolder::factory()->create([
            'name' => 'Parent',
            'path' => '/Parent/',
            'type' => FileOrFolderType::Folder,
        ]);

        $path = PathBuilder::build($parent);

        $this->assertEquals('/Parent/', $path);
    }

    #[Test]
    public function test_it_builds_full_path_for_a_folder()
    {
        $parent = FileOrFolder::factory()->create([
            'name' => 'Docs',
            'path' => '/Docs/',
            'type' => FileOrFolderType::Folder,
        ]);

        $folder = FileOrFolder::factory()->create([
            'name' => 'TVA',
            'parent_id' => $parent->id,
            'type' => FileOrFolderType::Folder,
        ]);

        $full = PathBuilder::buildFullPath($folder);

        $this->assertEquals('/Docs/TVA', $full);
    }

    #[Test]
    public function test_it_builds_full_path_for_a_file()
    {
        $parent = FileOrFolder::factory()->create([
            'name' => 'Docs',
            'path' => '/Docs/',
            'type' => FileOrFolderType::Folder,
        ]);

        $file = FileOrFolder::factory()->create([
            'name' => 'file.pdf',
            'parent_id' => $parent->id,
            'type' => FileOrFolderType::File,
        ]);

        $full = PathBuilder::buildFullPath($file);

        $this->assertEquals('/Docs(1)/file.pdf', $full);
    }

    #[Test]
    public function test_it_builds_minio_full_path_for_root()
    {
        $file = FileOrFolder::factory()->create([
            'name' => 'test.txt',
            'parent_id' => null,
        ]);

        $full = PathBuilder::buildMinioFullPath($file);

        $this->assertEquals('/test.txt', $full);
    }

    #[Test]
    public function test_it_builds_minio_full_path_for_child()
    {
        $folder = FileOrFolder::factory()->folder()->create();
        $file = FileOrFolder::factory()->create([
            'name' => 'test.txt',
            'parent_id' => $folder->id,
        ]);

        $full = PathBuilder::buildMinioFullPath($file);

        $this->assertEquals('/'.$folder->id.'/test.txt', $full);
    }
}
