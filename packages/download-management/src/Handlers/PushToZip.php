<?php

namespace Hopla\DownloadManagement\Handlers;

use App\Enums\FileOrFolderType;
use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\Handlers\PathBuilder;
use STS\ZipStream\Builder;
use STS\ZipStream\Exceptions\UnsupportedSourceDiskException;
use STS\ZipStream\Models\File;

class PushToZip
{
    /**
     * @param  FileOrFolder  $file
     * @param  Builder  $zip
     * @param  string  $prefix
     * @return void
     *
     * @throws UnsupportedSourceDiskException
     */
    public function addDescendents(FileOrFolder $file, Builder $zip, string $prefix): void
    {
        foreach ($file->childrens as $child) {
            $this->pushToZip($child, $zip, $prefix);
        }
    }

    /**
     * @param  FileOrFolder  $file
     * @param  Builder  $zip
     * @param  string  $prefix
     * @return void
     *
     * @throws UnsupportedSourceDiskException
     */
    private function pushToZip(FileOrFolder $file, Builder $zip, string $prefix): void
    {
        $pathInZip = $prefix.$file->name;
        if ($file->type === FileOrFolderType::Folder) {
            $this->addDescendents($file, $zip, $pathInZip.'/');

            return;
        }
        $file = File::makeFromDisk(
            'minio',
            PathBuilder::buildMinioFullPath($file),
            $pathInZip
        )
            ->setFilesize($file->size);
        $zip->add($file);

    }
}
