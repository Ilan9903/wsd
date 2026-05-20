<?php

namespace Hopla\HistoryManagement;

use Hopla\HistoryManagement\Providers\EventServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class HistoryManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/history-management.php',
            'history-management'
        );

        $this->app->register(EventServiceProvider::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/history-management.php' => config_path('history-management.php'),
        ]);

        $loader = AliasLoader::getInstance();
        $loader->alias('HistoryManager', Facades\HistoryManager::class);
    }
}
