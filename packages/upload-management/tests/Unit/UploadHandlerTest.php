<?php

namespace Tests\Unit;

use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\DTO\UploadedFile;
use Hopla\UploadManagement\Handlers\UploadHandler;
use Hopla\UploadManagement\Http\Requests\CompleteMultipartUploadRequest;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class UploadHandlerTest extends TenancyTestCase
{
    private UploadHandler $uploadHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uploadHandler = new UploadHandler;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function get_upload_base_folder_returns_null_when_parent_id_is_null(): void
    {
        /** @var UploadedFile&MockInterface $uploadedFile */
        $uploadedFile = Mockery::mock(UploadedFile::class);
        $uploadedFile->shouldReceive('getInfo')
            ->andReturn([
                'metadata' => [
                    'parentId' => 'null',
                ],
            ]);

        $resultBaseFolder = $this->uploadHandler->getUploadBaseFolder($uploadedFile);

        $this->assertNull($resultBaseFolder);
    }

    #[Test]
    public function get_upload_base_folder_returns_folder_when_parent_id_exists(): void
    {
        $folder = FileOrFolder::factory()->create();

        /** @var UploadedFile&MockInterface $uploadedFile */
        $uploadedFile = Mockery::mock(UploadedFile::class);
        $uploadedFile->shouldReceive('getInfo')
            ->andReturn([
                'metadata' => [
                    'parentId' => $folder->id,
                ],
            ]);

        $resultBaseFolder = $this->uploadHandler->getUploadBaseFolder($uploadedFile);

        $this->assertInstanceOf(FileOrFolder::class, $resultBaseFolder);
        $this->assertEquals($folder->id, $resultBaseFolder->id);
    }

    #[Test]
    public function get_upload_base_folder_returns_null_when_parent_id_does_not_exist(): void
    {
        /** @var UploadedFile&MockInterface $uploadedFile */
        $uploadedFile = Mockery::mock(UploadedFile::class);
        $uploadedFile->shouldReceive('getInfo')->andReturn([
            'metadata' => [
                'parentId' => 99999,
            ],
        ]);

        $resultBaseFolder = $this->uploadHandler->getUploadBaseFolder($uploadedFile);

        $this->assertNull($resultBaseFolder);
    }

    #[Test]
    public function get_uploaded_file_create_returns_uploaded_file_instance(): void
    {
        $resultUpload = [
            'UploadId' => 'test-upload-id-123',
        ];

        $resultUploadCreate = $this->uploadHandler->getUploadedFileCreate($resultUpload);

        $this->assertInstanceOf(UploadedFile::class, $resultUploadCreate);
        $this->assertEquals('test-upload-id-123', $resultUploadCreate->id);
    }

    #[Test]
    public function get_uploaded_file_complete_returns_uploaded_file_instance(): void
    {
        $resultUpload = new CompleteMultipartUploadRequest([
            'uploadId' => 'test-upload-id-123',
        ]);

        $resultUploadCreate = $this->uploadHandler->getUploadedFileComplete($resultUpload);

        $this->assertInstanceOf(UploadedFile::class, $resultUploadCreate);
        $this->assertEquals('test-upload-id-123', $resultUploadCreate->id);
    }

    #[Test]
    public function get_parts_maps_parts_with_uppercase_keys(): void
    {
        $completeRequest = new CompleteMultipartUploadRequest;
        $completeRequest->merge([
            'parts' => [
                ['PartNumber' => 1, 'ETag' => 'etag-1'],
                ['PartNumber' => 2, 'ETag' => 'etag-2'],
            ],
        ]);

        $resultParts = $this->uploadHandler->getParts($completeRequest);

        $expectedParts = [
            ['PartNumber' => 1, 'ETag' => 'etag-1'],
            ['PartNumber' => 2, 'ETag' => 'etag-2'],
        ];

        $this->assertEquals($expectedParts, $resultParts);
    }

    #[Test]
    public function get_parts_maps_parts_with_lowercase_keys(): void
    {
        $completeRequest = new CompleteMultipartUploadRequest;
        $completeRequest->merge([
            'parts' => [
                ['partNumber' => 1, 'etag' => 'etag-1'],
                ['partNumber' => 2, 'etag' => 'etag-2'],
            ],
        ]);

        $resultParts = $this->uploadHandler->getParts($completeRequest);

        $expectedParts = [
            ['PartNumber' => 1, 'ETag' => 'etag-1'],
            ['PartNumber' => 2, 'ETag' => 'etag-2'],
        ];

        $this->assertEquals($expectedParts, $resultParts);
    }

    #[Test]
    public function get_parts_maps_parts_with_mixed_case_keys(): void
    {
        $completeRequest = new CompleteMultipartUploadRequest;
        $completeRequest->merge([
            'parts' => [
                ['PartNumber' => 1, 'etag' => 'etag-1'],
                ['partNumber' => 2, 'ETag' => 'etag-2'],
            ],
        ]);

        $resultParts = $this->uploadHandler->getParts($completeRequest);

        $expectedParts = [
            ['PartNumber' => 1, 'ETag' => 'etag-1'],
            ['PartNumber' => 2, 'ETag' => 'etag-2'],
        ];

        $this->assertEquals($expectedParts, $resultParts);
    }

    #[Test]
    public function get_parts_returns_empty_array_when_no_parts(): void
    {
        $completeRequest = new CompleteMultipartUploadRequest;
        $completeRequest->merge([
            'parts' => [],
        ]);

        $resultParts = $this->uploadHandler->getParts($completeRequest);

        $this->assertEmpty($resultParts);
    }
}
