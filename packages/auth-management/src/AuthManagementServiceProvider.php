<?php

namespace Hopla\AuthManagement;

use Hopla\AuthManagement\Facades\AuthManager;
use Hopla\AuthManagement\Handlers\AuthManager as AuthHandler;
use Hopla\AuthManagement\Handlers\CredentialsValidator;
use Hopla\AuthManagement\Handlers\PasswordRenewalHandler;
use Hopla\AuthManagement\Handlers\TwoFactorHandler;
use Hopla\AuthManagement\Handlers\UserLoginHandler;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AuthManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/auth-management.php',
            'auth-management'
        );

        $this->app->singleton('AuthManager', function () {
            return new AuthHandler(
                new CredentialsValidator,
                new TwoFactorHandler,
                new PasswordRenewalHandler,
                new UserLoginHandler
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/auth-management.php' => config_path('auth-management.php'),
        ]);

        $loader = AliasLoader::getInstance();
        $loader->alias('AuthManager', AuthManager::class);
    }
}
