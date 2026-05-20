<?php

namespace Hopla\DownloadManagement;

use Hopla\DownloadManagement\Facades\DownloadManager;
use Hopla\DownloadManagement\Handlers\DownloadFile;
use Hopla\DownloadManagement\Handlers\DownloadFolder;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class DownloadManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/download-management.php',
            'download-management'
        );

        $this->app->singleton('DownloadManager', function () {
            return new Handlers\DownloadManager(
                new DownloadFile,
                new DownloadFolder
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/download-management.php' => config_path('download-management.php'),
        ]);

        $loader = AliasLoader::getInstance();
        $loader->alias('DownloadManager', DownloadManager::class);
    }
}
