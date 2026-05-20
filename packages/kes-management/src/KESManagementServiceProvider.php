<?php

namespace Hopla\KesManagement;

use Hopla\KesManagement\Handler\KesKeyManager;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class KESManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/kes-management.php',
            'kes-management'
        );

        $this->app->singleton('kes-key', function () {
            return new KesKeyManager;
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/kes-management.php' => config_path('kes-management.php'),
        ], 'kes-management-config');

        $loader = AliasLoader::getInstance();
        $loader->alias('KesKeyManager', KesKeyManager::class);
    }
}
