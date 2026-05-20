<?php

namespace Hopla\DownloadManagement\Handlers;

use App\Models\FileOrFolder;
use STS\ZipStream\Builder;
use STS\ZipStream\Exceptions\UnsupportedSourceDiskException;

class DownloadManager
{
    public function __construct(
        protected DownloadFile $downloadFile,
        protected DownloadFolder $downloadFolder,

    ) {}

    /**
     * @param  FileOrFolder  $file
     * @return string
     *
     * @throws \Exception
     */
    public function downloadFile(FileOrFolder $file): string
    {
        return $this->downloadFile->downloadFile($file);
    }

    /**
     * @param  FileOrFolder  $file
     * @return Builder
     *
     * @throws UnsupportedSourceDiskException
     */
    public function downloadFolder(FileOrFolder $file): Builder
    {
        return $this->downloadFolder->downloadFolder($file);
    }
}
