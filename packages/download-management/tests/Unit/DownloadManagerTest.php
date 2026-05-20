<?php

namespace Tests\Unit;

use App\Models\FileOrFolder;
use Hopla\DownloadManagement\Handlers\DownloadFile;
use Hopla\DownloadManagement\Handlers\DownloadFolder;
use Hopla\DownloadManagement\Handlers\DownloadManager;
use Hopla\DownloadManagement\Handlers\PushToZip;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use STS\ZipStream\Builder;
use STS\ZipStream\Facades\Zip;
use Tests\Utils\TenancyTestCase;

class DownloadManagerTest extends TenancyTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function test_download_file_returns(): void
    {
        $file = FileOrFolder::factory()->create([
            'type' => 'file',
            'name' => 'test.zip',
        ]);

        Storage::shouldReceive('disk')
            ->with('minio')
            ->andReturnSelf();

        Storage::shouldReceive('exists')
            ->andReturnTrue();

        Storage::shouldReceive('temporaryUrl')
            ->andReturn('signed-url');

        $manager = new DownloadManager(
            new DownloadFile,
            new DownloadFolder
        );

        $resultFile = $manager->downloadFile($file);

        $this->assertSame('signed-url', $resultFile);
    }

    #[Test]
    public function test_download_folder_returns(): void
    {
        $folder = FileOrFolder::factory()->create([
            'type' => 'folder',
            'name' => 'docs',
        ]);

        Zip::shouldReceive('create')
            ->andReturn(Mockery::mock(Builder::class));

        Mockery::mock('overload:'.PushToZip::class)
            ->shouldReceive('addDescendents')
            ->andReturnNull();

        $manager = new DownloadManager(
            new DownloadFile,
            new DownloadFolder
        );

        $resultFolder = $manager->downloadFolder($folder);

        $this->assertInstanceOf(Builder::class, $resultFolder);
    }
}
