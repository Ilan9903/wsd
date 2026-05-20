<?php

namespace Hopla\DownloadManagement\Handlers;

use App\Models\FileOrFolder;
use Illuminate\Support\Str;
use STS\ZipStream\Builder;
use STS\ZipStream\Exceptions\UnsupportedSourceDiskException;
use STS\ZipStream\Facades\Zip;

class DownloadFolder
{
    /**
     * @param  FileOrFolder  $folder
     * @return Builder
     *
     * @throws UnsupportedSourceDiskException
     */
    public function downloadFolder(FileOrFolder $folder): Builder
    {
        $zipName = $folder->name.'-'.Str::uuid().'.zip';
        $zip = Zip::create($zipName);
        (new PushToZip)->addDescendents($folder, $zip, '');

        return $zip;
    }
}
