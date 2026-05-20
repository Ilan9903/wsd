<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Support\Facades\Facade;
use Tests\TestBootstrap;

// 1) Load Composer’s autoloader
require_once __DIR__.'/../vendor/autoload.php';

$bootstrap = new TestBootstrap;

function startKernel($bootstrap): void
{
    Facade::clearResolvedInstances();
    $bootstrap->app = require __DIR__.'/../bootstrap/app.php';
    $bootstrap->app->make(Kernel::class)->bootstrap();
}

function endKernel($bootstrap): void
{
    $bootstrap->app->flush();
    $bootstrap->app = null;
    HandleExceptions::flushState();
}

function start(&$bootstrap): void
{

    echo "Initialisation des tests...\n";

    startKernel($bootstrap);
    $bootstrap->createTestingTenant();
    endKernel($bootstrap);
}

register_shutdown_function(function () use ($bootstrap) {

    echo "Fin des tests, nettoyage en cours...\n";

    startKernel($bootstrap);
    $bootstrap->removeTestingTenant();
    endKernel($bootstrap);

    echo "Tests terminés !\n";

});

start($bootstrap);
