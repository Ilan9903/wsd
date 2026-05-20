<?php

namespace Hopla\HistoryManagement\Facades;

use Illuminate\Support\Facades\Facade;

class HistoryManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'history-manager';
    }
}
