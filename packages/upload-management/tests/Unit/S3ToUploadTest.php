<?php

namespace Tests\Unit;

use App\Enums\FileOrFolderType;
use App\Models\User;
use Hopla\FileOrFolderManagement\DTO\UploadedFile;
use Hopla\UploadManagement\Handlers\S3ToUpload;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class S3ToUploadTest extends TenancyTestCase
{
    #[Test]
    public function test_save_file_to_s3_works(): void
    {
        Storage::fake('minio');

        $user = User::factory()->create();
        $this->actingAs($user);

        $tempPath = 'tmp/file.txt';
        Storage::disk('minio')->put($tempPath, 'content');

        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->method('getBaseName')->willReturn('file.txt');
        $uploadedFile->method('getLength')->willReturn(25364);
        $uploadedFile->method('getMimeType')->willReturn('text/plain');
        $uploadedFile->method('getTempPath')->willReturn($tempPath);
        $uploadedFile->expects($this->once())->method('removeInfo');

        $handler = new S3ToUpload;

        $resultS3 = $handler->saveFileToS3(null, $uploadedFile);

        $this->assertEquals('file.txt', $resultS3->name);
        $this->assertEquals(FileOrFolderType::File, $resultS3->type);
        $this->assertDatabaseHas('file_or_folders', [
            'id' => $resultS3->id,
        ]);

        $this->assertFalse(Storage::disk('minio')->exists($tempPath));
    }
}
