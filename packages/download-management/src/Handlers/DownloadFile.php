<?php

namespace Hopla\DownloadManagement\Handlers;

use App\Models\FileOrFolder;
use Hopla\DownloadManagement\Exceptions\FileNotFoundInMinioException;
use Hopla\FileOrFolderManagement\Handlers\PathBuilder;
use Illuminate\Support\Facades\Storage;

class DownloadFile
{
    /**
     * @param  FileOrFolder  $file
     * @return string
     *
     * @throws \Exception
     */
    public function downloadFile(FileOrFolder $file): string
    {

        $storage = Storage::disk('minio');
        $relativePath = PathBuilder::buildMinioFullPath($file);
        if (! $storage->exists($relativePath)) {
            throw new FileNotFoundInMinioException($relativePath);
        }

        return $storage->temporaryUrl(
            $relativePath,
            now()->addMinutes(5),
            [
                'ResponseContentDisposition' => 'attachment; filename="'.$file->name.'"',
                'ResponseContentType' => 'application/octet-stream',
            ]
        );
    }
}
