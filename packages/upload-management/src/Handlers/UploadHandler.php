<?php

namespace Hopla\UploadManagement\Handlers;

use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\DTO\UploadedFile;
use Hopla\UploadManagement\Http\Requests\CompleteMultipartUploadRequest;

class UploadHandler
{
    /**
     * @param  UploadedFile  $uploadedFile
     * @return FileOrFolder|null
     */
    public function getUploadBaseFolder(UploadedFile $uploadedFile): ?FileOrFolder
    {
        $metadata = $uploadedFile->getInfo()['metadata'];
        $parentId = $metadata['parentId'];

        if ($metadata['parentId'] === null) {
            return null;
        }

        /** @var FileOrFolder|null $folder */
        $folder = FileOrFolder::find($parentId);

        return $folder;
    }

    /**
     * @param  array<string, mixed>  $resultUpload
     * @return UploadedFile
     */
    public function getUploadedFileCreate($resultUpload): UploadedFile
    {

        return new UploadedFile($resultUpload['UploadId'], UploadedFile::$uploadPath);
    }

    /**
     * @param  CompleteMultipartUploadRequest  $completeRequest
     * @return UploadedFile
     */
    public function getUploadedFileComplete(CompleteMultipartUploadRequest $completeRequest): UploadedFile
    {
        return new UploadedFile($completeRequest->uploadId, UploadedFile::$uploadPath);
    }

    /**
     * @param  CompleteMultipartUploadRequest  $completeRequest
     * @return array<int, array<string, int|string>>
     */
    public function getParts(CompleteMultipartUploadRequest $completeRequest)
    {
        /** @var array<int, array<string, int|string>> $part */
        return collect($completeRequest->parts)->map(fn ($part) => [
            'PartNumber' => $part['PartNumber'] ?? $part['partNumber'],
            'ETag' => $part['ETag'] ?? $part['etag'],
        ])->toArray();
    }
}
