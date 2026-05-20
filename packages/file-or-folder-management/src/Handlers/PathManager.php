<?php

namespace Hopla\FileOrFolderManagement\Handlers;

use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\DTO\UploadedFile;

class PathManager
{
    /**
     * @param  UploadedFile  $file
     * @return FileOrFolder|null
     */
    public function getUploadBaseFolder(UploadedFile $file): ?FileOrFolder
    {

        $metadata = $file->getInfo()['metadata'];
        $parentId = (string) $metadata['parentId'];

        if ($metadata['parentId'] == 'null') {
            return null;
        }

        return FileOrFolder::find($parentId);
    }

    /**
     * @param  string  $relativePath
     * @return array<string>
     */
    public static function getPathParts(string $relativePath): array
    {
        $parts = explode('/', $relativePath);
        array_pop($parts);

        return $parts;
    }
}
