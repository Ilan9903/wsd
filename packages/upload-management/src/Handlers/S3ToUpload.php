<?php

namespace Hopla\UploadManagement\Handlers;

use App\Enums\FileOrFolderType;
use App\Models\FileOrFolder;
use App\Services\Minio\Bucket;
use Hopla\FileOrFolderManagement\DTO\UploadedFile;
use Hopla\FileOrFolderManagement\Handlers\PathBuilder;
use Illuminate\Support\Facades\Storage;

class S3ToUpload
{
    /**
     * @param  FileOrFolder|null  $parentFolder
     * @param  UploadedFile  $uploadedFile
     * @return FileOrFolder
     */
    public function saveFileToS3(?FileOrFolder $parentFolder, UploadedFile $uploadedFile): FileOrFolder
    {
        $s3 = Storage::disk('minio');
        (new Bucket)->getBucket();
        $storagePath = PathBuilder::build($parentFolder);

        $fileSystemObject = FileOrFolder::create([
            'user_id' => $parentFolder?->user->id ?? auth()->id(),
            'name' => $uploadedFile->getBaseName(),
            'path' => $storagePath,
            'type' => FileOrFolderType::File,
            'size' => $uploadedFile->getLength(),
            'mime_type' => $uploadedFile->getMimeType(),
            'parent_id' => $parentFolder?->id,
        ]);

        $s3Path = PathBuilder::buildMinioPath($fileSystemObject);
        $tempPath = $uploadedFile->getTempPath();

        if ($s3->exists($tempPath)) {
            $s3->copy($tempPath, $s3Path.$fileSystemObject->name);
            $s3->delete($tempPath);
        }

        $uploadedFile->removeInfo();

        return $fileSystemObject;
    }
}
