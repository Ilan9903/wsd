<?php

namespace Hopla\FileOrFolderManagement\Services;

use App\Enums\FileOrFolderType;
use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\Handlers\PathBuilder;

class FileOrFolderService
{
    public static function generateUniqueName(
        FileOrFolder $fileSystemItem,
        ?FileOrFolder $inFolder
    ): string {
        if ($fileSystemItem->type === FileOrFolderType::Folder) {
            return FileOrFolderService::generateUniqueFolderName(
                PathBuilder::build($inFolder),
                $fileSystemItem->name,
            );
        }

        return FileOrFolderService::generateUniqueFileName(
            PathBuilder::build($inFolder),
            pathinfo($fileSystemItem->name, PATHINFO_FILENAME),
            pathinfo($fileSystemItem->name, PATHINFO_EXTENSION),
        );

    }

    public static function fileSystemItemExists(
        string $parentPath,
        string $fullName
    ): bool {
        return FileOrFolder::where('path', '=', $parentPath)
            ->where('name', '=', $fullName)
            ->withTrashed()
            ->exists();
    }

    public static function generateUniqueFileName(
        string $parentPath,
        string $originalName,
        string $extension,
    ): string {

        $filename = $originalName;
        $counter = 0;

        while (self::fileSystemItemExists($parentPath, $filename.'.'.$extension)) {
            $counter++;
            $filename = $originalName."($counter)";
        }

        return $filename.'.'.$extension;
    }

    public static function generateUniqueFolderName(
        string $parentPath,
        string $originalName,
    ): string {

        $folderName = $originalName;
        $counter = 0;

        while (self::fileSystemItemExists($parentPath, $folderName)) {
            $counter++;
            $folderName = $originalName."($counter)";
        }

        return $folderName;
    }
}
