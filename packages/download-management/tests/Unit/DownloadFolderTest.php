<?php

namespace Tests\Unit;

use App\Models\FileOrFolder;
use Hopla\DownloadManagement\Handlers\DownloadFolder;
use Hopla\DownloadManagement\Handlers\PushToZip;
use Illuminate\Support\Str;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use STS\ZipStream\Builder;
use STS\ZipStream\Facades\Zip;
use Tests\Utils\TenancyTestCase;

class DownloadFolderTest extends TenancyTestCase
{
    private DownloadFolder $downloadFolder;

    private FileOrFolder $folder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->downloadFolder = new DownloadFolder;

        $this->folder = FileOrFolder::factory()->create([
            'type' => 'folder',
            'name' => 'test-folder',
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function test_it_returns_zip_builder_instance(): void
    {
        Zip::shouldReceive('create')
            ->andReturn(Mockery::mock(Builder::class));

        $pushToZipMock = Mockery::mock('overload:'.PushToZip::class);
        $pushToZipMock->shouldReceive('addDescendents')->andReturnNull();

        $resultFolder = $this->downloadFolder->downloadFolder($this->folder);

        $this->assertInstanceOf(Builder::class, $resultFolder);
    }

    #[Test]
    public function test_it_creates_zip_with_folder_name_and_uuid(): void
    {
        $zipNamePattern = $this->folder->name.'-';

        Zip::shouldReceive('create')->withArgs(function ($zipName) use ($zipNamePattern) {
            return Str::startsWith($zipNamePattern, $zipNamePattern)
                && Str::endsWith($zipName, '.zip')
                && Str::isUuid(Str::between($zipName, $zipNamePattern, '.zip'));
        })->andReturn(Mockery::mock(Builder::class));

        $pushToZipMock = Mockery::mock('overload:'.PushToZip::class);
        $pushToZipMock->shouldReceive('addDescendents')->andReturnNull();

        $this->downloadFolder->downloadFolder($this->folder);

        $this->assertTrue(true);
    }

    #[Test]
    public function test_it_calls_push_to_zip_with_correct_parameters(): void
    {
        $zipMock = Mockery::mock(Builder::class);

        Zip::shouldReceive('create')->andReturn($zipMock);

        $pushToZipMock = Mockery::mock('overload:'.PushToZip::class);
        $pushToZipMock->shouldReceive('addDescendents')->withArgs(function ($folder, $zip, $path) use ($zipMock) {
            return $folder->id === $this->folder->id
                && $zip === $zipMock
                && $path === '';
        })->andReturnNull();

        $this->downloadFolder->downloadFolder($this->folder);

        $this->assertTrue(true);
    }

    #[Test]
    public function test_it_downloads_folder_with_special_characters_in_name(): void
    {
        $folderWithSpecialChars = FileOrFolder::factory()->create([
            'type' => 'folder',
            'name' => 't€st-&àé_t€st',
        ]);

        $zipNamePattern = 't€st-&àé_t€st';

        Zip::shouldReceive('create')->withArgs(function ($zipName) use ($zipNamePattern) {
            return Str::startsWith($zipName, $zipNamePattern)
                && Str::endsWith($zipName, '.zip');
        })->andReturn(Mockery::mock(Builder::class));

        $pushToZipMock = Mockery::mock('overload:'.PushToZip::class);
        $pushToZipMock->shouldReceive('addDescendents')->andReturnNull();

        $resultFolder = $this->downloadFolder->downloadFolder($folderWithSpecialChars);

        $this->assertInstanceOf(Builder::class, $resultFolder);
    }

    #[Test]
    public function test_it_creates_unique_zip_names_for_same_folder(): void
    {
        $zipNames = [];

        $pushToZipMock = Mockery::mock('overload:'.PushToZip::class);
        $pushToZipMock->shouldReceive('addDescendents')->andReturnNull();

        Zip::shouldReceive('create')->twice()->andReturnUsing(function ($zipName) use (&$zipNames) {
            $zipNames[] = $zipName;

            return Mockery::mock(Builder::class);
        });

        $this->downloadFolder->downloadFolder($this->folder);
        $this->downloadFolder->downloadFolder($this->folder);

        $this->assertCount(2, $zipNames);
        $this->assertNotEquals($zipNames[0], $zipNames[1]);
        $this->assertStringStartsWith($this->folder->name.'-', $zipNames[0]);
        $this->assertStringStartsWith($this->folder->name.'-', $zipNames[1]);
    }
}
