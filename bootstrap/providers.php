<?php

use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\Filament\SupportPanelProvider;
use App\Providers\HorizonServiceProvider;
use App\Providers\TelescopeServiceProvider;
use App\Providers\TenancyServiceProvider;

return [
    AppServiceProvider::class,
    EventServiceProvider::class,
    SupportPanelProvider::class,
    HorizonServiceProvider::class,
    TelescopeServiceProvider::class,
    TenancyServiceProvider::class,
    Hopla\FileOrFolderManagement\Providers\EventServiceProvider::class,
    Hopla\HistoryManagement\Providers\EventServiceProvider::class,
];
