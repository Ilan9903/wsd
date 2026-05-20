<?php

namespace Hopla\FileOrFolderManagement;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class FileOrFolderManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/file-or-folder-management.php',
            'file-or-folder-management'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/file-or-folder-management.php' => config_path('file-or-folder-management.php'),
        ]);

        $loader = AliasLoader::getInstance();
        $loader->alias('FileOrFolderManager', Facades\FileOrFolderManager::class);
    }
}
