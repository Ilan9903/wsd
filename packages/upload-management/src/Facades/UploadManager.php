<?php

namespace Hopla\UploadManagement\Facades;

use Illuminate\Support\Facades\Facade;
class UploadManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'UploadManager';
    }
}
