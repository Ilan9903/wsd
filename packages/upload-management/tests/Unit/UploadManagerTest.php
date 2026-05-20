<?php

namespace Tests\Unit;

use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\DTO\UploadedFile;
use Hopla\UploadManagement\Handlers\S3ToUpload;
use Hopla\UploadManagement\Handlers\UploadHandler;
use Hopla\UploadManagement\Handlers\UploadManager;
use Hopla\UploadManagement\Http\Requests\CompleteMultipartUploadRequest;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class UploadManagerTest extends TenancyTestCase
{
    private UploadManager $uploadManager;

    private S3ToUpload $s3ToUpload;

    private UploadHandler $uploadHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->s3ToUpload = $this->createMock(S3ToUpload::class);
        $this->uploadHandler = $this->createMock(UploadHandler::class);

        $this->uploadManager = new UploadManager(
            $this->s3ToUpload,
            $this->uploadHandler
        );
    }

    #[Test]
    public function test_get_upload_base_folder_returns(): void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);
        $folder = $this->createMock(FileOrFolder::class);

        $this->uploadHandler
            ->expects($this->once())
            ->method('getUploadBaseFolder')
            ->with($uploadedFile)
            ->willReturn($folder);

        $resultBaseFolder = $this->uploadManager->getUploadBaseFolder($uploadedFile);

        $this->assertSame($folder, $resultBaseFolder);
    }

    #[Test]
    public function test_get_upload_base_folder_returns_null(): void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);

        $this->uploadHandler
            ->expects($this->once())
            ->method('getUploadBaseFolder')
            ->with($uploadedFile)
            ->willReturn(null);

        $resultBaseFolder = $this->uploadManager->getUploadBaseFolder($uploadedFile);

        $this->assertNull($resultBaseFolder);
    }

    #[Test]
    public function test_get_uploaded_file_create_returns(): void
    {
        $resultUpload = ['key' => 'value'];
        $uploadedFile = $this->createMock(UploadedFile::class);

        $this->uploadHandler
            ->expects($this->once())
            ->method('getUploadedFileCreate')
            ->with($resultUpload)
            ->willReturn($uploadedFile);

        $resultCreate = $this->uploadManager->getUploadedFileCreate($resultUpload);

        $this->assertInstanceOf(UploadedFile::class, $resultCreate);
        $this->assertSame($uploadedFile, $resultCreate);
    }

    #[Test]
    public function test_get_uploaded_file_complete_returns(): void
    {
        $completeRequest = new CompleteMultipartUploadRequest;
        $uploadedFile = $this->createMock(UploadedFile::class);

        $this->uploadHandler
            ->expects($this->once())
            ->method('getUploadedFileComplete')
            ->with($completeRequest)
            ->willReturn($uploadedFile);

        $resultComplete = $this->uploadManager->getUploadedFileComplete($completeRequest);

        $this->assertInstanceOf(UploadedFile::class, $resultComplete);
        $this->assertSame($uploadedFile, $resultComplete);
    }

    #[Test]
    public function test_get_parts_returns(): void
    {
        $completeRequest = new CompleteMultipartUploadRequest;
        $parts = [
            ['PartNumber' => 1, 'ETag' => 'etag1'],
            ['PartNumber' => 2, 'ETag' => 'etag2'],
        ];

        $this->uploadHandler
            ->expects($this->once())
            ->method('getParts')
            ->with($completeRequest)
            ->willReturn($parts);

        $resultParts = $this->uploadManager->getParts($completeRequest);

        $this->assertIsArray($resultParts);
        $this->assertSame($parts, $resultParts);
    }

    #[Test]
    public function test_save_file_to_s3_with_parent_folder(): void
    {
        $parentFolder = $this->createMock(FileOrFolder::class);
        $uploadedFile = $this->createMock(UploadedFile::class);
        $fileOrFolder = $this->createMock(FileOrFolder::class);

        $this->s3ToUpload
            ->expects($this->once())
            ->method('saveFileToS3')
            ->with($parentFolder, $uploadedFile)
            ->willReturn($fileOrFolder);

        $resultS3 = $this->uploadManager->saveFileToS3($parentFolder, $uploadedFile);

        $this->assertInstanceOf(FileOrFolder::class, $resultS3);
        $this->assertSame($fileOrFolder, $resultS3);
    }

    #[Test]
    public function test_save_file_to_s3_without_parent_folder(): void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);
        $fileOrFolder = $this->createMock(FileOrFolder::class);

        $this->s3ToUpload
            ->expects($this->once())
            ->method('saveFileToS3')
            ->with(null, $uploadedFile)
            ->willReturn($fileOrFolder);

        $resultS3 = $this->uploadManager->saveFileToS3(null, $uploadedFile);

        $this->assertInstanceOf(FileOrFolder::class, $resultS3);
        $this->assertSame($fileOrFolder, $resultS3);
    }
}
