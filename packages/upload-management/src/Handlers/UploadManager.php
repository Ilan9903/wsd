<?php

namespace Hopla\UploadManagement\Handlers;

use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\DTO\UploadedFile;
use Hopla\UploadManagement\Http\Requests\CompleteMultipartUploadRequest;

class UploadManager
{
    public function __construct(
        protected S3ToUpload $s3ToUpload,
        protected UploadHandler $uploadHandler,
    ) {}

    public function getUploadBaseFolder(UploadedFile $uploadedFile): ?FileOrFolder
    {
        return $this->uploadHandler->getUploadBaseFolder($uploadedFile);
    }

    public function getUploadedFileCreate($resultUpload): UploadedFile
    {
        return $this->uploadHandler->getUploadedFileCreate($resultUpload);
    }

    public function getUploadedFileComplete(CompleteMultipartUploadRequest $completeRequest): UploadedFile
    {
        return $this->uploadHandler->getUploadedFileComplete($completeRequest);
    }

    public function getParts(CompleteMultipartUploadRequest $completeRequest): array
    {
        return $this->uploadHandler->getParts($completeRequest);
    }

    public function saveFileToS3(?FileOrFolder $parentFolder, UploadedFile $uploadedFile): FileOrFolder
    {
        return $this->s3ToUpload->saveFileToS3($parentFolder, $uploadedFile);
    }
}
