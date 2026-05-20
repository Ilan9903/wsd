<?php

namespace Hopla\AuthManagement\Facades;

use Illuminate\Support\Facades\Facade;

class AuthManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'AuthManager';
    }
}
