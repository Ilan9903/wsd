<?php

namespace Hopla\UploadManagement;

use Hopla\UploadManagement\Handlers\S3ToUpload;
use Hopla\UploadManagement\Handlers\UploadHandler;
use Hopla\UploadManagement\Handlers\UploadManager;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class UploadManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/upload-management.php',
            'upload-management'
        );

        $this->app->singleton('UploadManager', function () {
            return new UploadManager(
                new S3ToUpload,
                new UploadHandler
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/upload-management.php' => config_path('upload-management.php'),
        ]);

        $loader = AliasLoader::getInstance();
        $loader->alias('UploadManager', Facades\UploadManager::class);
    }
}
