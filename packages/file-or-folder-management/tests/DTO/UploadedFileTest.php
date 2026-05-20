<?php

namespace Tests\Unit\Hopla\FileOrFolderManagement\DTO;

use Hopla\FileOrFolderManagement\DTO\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\Utils\TenancyTestCase;

class UploadedFileTest extends TenancyTestCase
{
    #[Test]
    public function it_initializes_uploaded_file()
    {
        Storage::fake('local');

        $file = new UploadedFile('12345', '/uploads');

        $this->assertEquals('/uploads', $file->path);
        $this->assertEquals('/uploads/12345', $file->filePath);
        $this->assertEquals('/uploads/12345.info', $file->infoPath);
        $this->assertIsArray($file->getMetadata());
    }

    #[Test]
    public function it_detects_non_existing_info_file()
    {
        Storage::fake('local');

        $file = new UploadedFile('12345', '/uploads');

        $this->assertFalse($file->exists());
    }

    #[Test]
    public function it_creates_file_info_on_disk_and_reads_it()
    {
        Storage::fake('local');

        $file = new UploadedFile('12345', '/uploads');

        $meta = [
            'name' => 'document.pdf',
            'type' => 'application/pdf',
            'size' => 1024,
            'tempPath' => '/tmp/file.tmp',
        ];

        $file->createOnDisk('2048', $meta);

        $this->assertTrue($file->exists());
        $this->assertEquals('2048', $file->getLength());
        $this->assertEquals('document', $file->getName());
        $this->assertEquals('pdf', $file->getExtension());
        $this->assertEquals('application/pdf', $file->getMimeType());
        $this->assertEquals(0, $file->getOffset());
        $this->assertEquals(1024, $file->getSize());
        $this->assertEquals('/tmp/file.tmp', $file->getTempPath());

        $info = $file->info();
        $this->assertIsArray($info);
        $this->assertEquals('document.pdf', $info['metadata']['name']);
    }

    #[Test]
    public function it_removes_info_file()
    {
        Storage::fake('local');

        $file = new UploadedFile('12345', '/uploads');

        $meta = [
            'name' => 'file.txt',
            'type' => 'text/plain',
        ];

        $file->createOnDisk('100', $meta);

        $this->assertTrue($file->exists());

        $file->removeInfo();

        $this->assertFalse($file->exists());
    }

    #[Test]
    public function it_returns_null_for_missing_size_or_temp_path()
    {
        Storage::fake('local');

        $file = new UploadedFile('12345', '/uploads');

        $meta = [
            'name' => 'file.txt',
            'type' => 'text/plain',
        ];

        $file->createOnDisk('50', $meta);

        $this->assertNull($file->getSize());
        $this->assertNull($file->getTempPath());
    }
}
