<?php

namespace Hopla\FileOrFolderManagement\Facades;

use Illuminate\Support\Facades\Facade;

class FileOrFolderManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'file-or-folder-manager';
    }
}
