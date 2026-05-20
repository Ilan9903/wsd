<?php

namespace Hopla\GatewayManagement;

use Hopla\GatewayManagement\Handlers\GatewayClient;
use Hopla\GatewayManagement\Handlers\GatewayManager;
use Hopla\GatewayManagement\Handlers\GatewayUser;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class GatewayManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/gateway-management.php',
            'gateway-management'
        );

        $this->app->singleton('GatewayManager', function () {
            return new GatewayManager(
                new GatewayClient,
                new GatewayUser,
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/gateway-management.php' => config_path('gateway-management.php'),
        ]);

        $loader = AliasLoader::getInstance();
        $loader->alias('GatewayManager', GatewayManager::class);
    }
}
