<?php

namespace Hopla\DownloadManagement\Facades;

use Illuminate\Support\Facades\Facade;

class DownloadManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'DownloadManager';
    }
}
