<?php

namespace Hopla\KesManagement\Facades;

use Illuminate\Support\Facades\Facade;

class KESManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'kes-key';
    }
}
