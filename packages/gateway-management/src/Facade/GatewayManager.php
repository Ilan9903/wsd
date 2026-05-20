<?php

namespace Hopla\GatewayManagement\Facade;

use Illuminate\Support\Facades\Facade;

class GatewayManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'GatewayManager';
    }
}
