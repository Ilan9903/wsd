<?php

namespace Hopla\FileOrFolderManagement\Handlers;

use App\Enums\FileOrFolderType;
use App\Models\FileOrFolder;

class PathBuilder
{
    public static function build(?FileOrFolder $parentFolder): string
    {
        if (! $parentFolder) {
            return '/';
        }

        return $parentFolder->path.$parentFolder->name.'/';
    }

    public static function buildFullPath(FileOrFolder $fileOrFolder): string
    {
        $parentPath = self::build($fileOrFolder->parent);

        if ($fileOrFolder->type === FileOrFolderType::Folder) {
            return $parentPath.$fileOrFolder->name;
        }

        return $parentPath.$fileOrFolder->name;
    }

    public static function buildMinioPath(FileOrFolder $fileOrFolder): string
    {
        if (! $fileOrFolder->parent_id) {
            return '/';
        }

        return $fileOrFolder->parent_id.'/';
    }

    public static function buildMinioFullPath(FileOrFolder $fileOrFolder): string
    {
        $parentPath = '/';

        if ($fileOrFolder->parent_id) {
            $parentPath .= $fileOrFolder->parent_id.'/';
        }

        return $parentPath.$fileOrFolder->name;
    }
}
