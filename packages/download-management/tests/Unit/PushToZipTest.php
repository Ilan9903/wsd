<?php

namespace Tests\Unit;

use App\Enums\FileOrFolderType;
use App\Models\FileOrFolder;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\Datasets\Test\EmptyDataSet;
use Tests\Utils\TenancyTestCase;

class PushToZipTest extends TenancyTestCase
{
    protected function getDatasetClass(): string
    {
        return EmptyDataSet::class;
    }

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('minio');
    }

    #[Test]
    public function test_it_processes_file_structure(): void
    {
        $rootFolder = FileOrFolder::factory()->create([
            'type' => FileOrFolderType::Folder,
            'name' => 'root',
        ]);

        $file = FileOrFolder::factory()->create([
            'type' => FileOrFolderType::File,
            'name' => 'file.txt',
            'size' => 1234,
            'parent_id' => $rootFolder->id,
        ]);

        $rootFolder->setRelation('childrens', collect([$file]));

        $this->assertCount(1, $rootFolder->childrens);
        $this->assertEquals(FileOrFolderType::File, $file->type);
        $this->assertEquals('file.txt', $file->name);
    }

    #[Test]
    public function test_it_handles_nested_folder_structure(): void
    {
        $rootFolder = FileOrFolder::factory()->create([
            'type' => FileOrFolderType::Folder,
            'name' => 'root',
        ]);

        $subFolder = FileOrFolder::factory()->create([
            'type' => FileOrFolderType::Folder,
            'name' => 'subfolder',
            'parent_id' => $rootFolder->id,
        ]);

        $file = FileOrFolder::factory()->create([
            'type' => FileOrFolderType::File,
            'name' => 'file.txt',
            'size' => 1234,
            'parent_id' => $subFolder->id,
        ]);

        $rootFolder->setRelation('childrens', collect([$subFolder]));
        $subFolder->setRelation('childrens', collect([$file]));

        $this->assertCount(1, $rootFolder->childrens);
        $this->assertEquals(FileOrFolderType::Folder, $subFolder->type);
        $this->assertCount(1, $subFolder->childrens);
        $this->assertEquals('file.txt', $file->name);
    }

    #[Test]
    public function test_it_handles_empty_folder_structure(): void
    {
        $folder = FileOrFolder::factory()->create([
            'type' => FileOrFolderType::Folder,
            'name' => 'empty',
        ]);

        $folder->setRelation('childrens', collect([]));

        $this->assertCount(0, $folder->childrens);
        $this->assertEquals(FileOrFolderType::Folder, $folder->type);
    }
}
