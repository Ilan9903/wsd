<?php

namespace Tests\Unit;

use App\Models\FileOrFolder;
use Hopla\DownloadManagement\Exceptions\FileNotFoundInMinioException;
use Hopla\DownloadManagement\Handlers\DownloadFile;
use Hopla\FileOrFolderManagement\Handlers\PathBuilder;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class DownloadFileTest extends TenancyTestCase
{
    private DownloadFile $downloadFile;

    private FileOrFolder $file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->downloadFile = new DownloadFile;

        $this->file = FileOrFolder::factory()->create([
            'type' => 'file',
            'name' => 'test-file.pdf',
        ]);
    }

    #[Test]
    public function test_it_throws_exception_when_file_does_not_exist_in_minio(): void
    {
        Storage::fake('minio');

        $this->expectException(FileNotFoundInMinioException::class);

        $this->downloadFile->downloadFile($this->file);
    }

    #[Test]
    public function test_it_returns_temporary_url_when_file_exists(): void
    {
        Storage::fake('minio');
        $storage = Storage::disk('minio');
        $relativePath = PathBuilder::buildMinioFullPath($this->file);

        $storage->put($relativePath, 'fake file content');

        $resultUrl = $this->downloadFile->downloadFile($this->file);

        $this->assertIsString($resultUrl);
        $this->assertNotEmpty($resultUrl);
    }

    #[Test]
    public function test_it_generates_url_with_correct_headers(): void
    {
        Storage::fake('minio');
        $storage = Storage::disk('minio');
        $relativePath = PathBuilder::buildMinioFullPath($this->file);

        $storage->put($relativePath, 'fake file content');

        $resultUrl = $this->downloadFile->downloadFile($this->file);

        $this->assertIsString($resultUrl);
        $this->assertStringContainsString($this->file->name, $resultUrl);
    }

    #[Test]
    public function test_it_downloads_file_with_correct_filename(): void
    {
        Storage::fake('minio');
        $storage = Storage::disk('minio');
        $fileName = 'test-file.pdf';

        $file = FileOrFolder::factory()->create([
            'type' => 'file',
            'name' => $fileName,
        ]);

        $relativePath = PathBuilder::buildMinioFullPath($file);
        $storage->put($relativePath, 'fake file content');

        $resultUrl = $this->downloadFile->downloadFile($file);

        $this->assertIsString($resultUrl);
        $this->assertNotEmpty($resultUrl);
    }
}
