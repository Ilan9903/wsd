<?php

use App\Models\Client;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('model:prune', [
    '----model' => [Client::class],
])->dailyAt('00:20');

Schedule::command('app:set-inactive-link-expired')->hourlyAt(1);
Schedule::command('app:set-inactive-link-expired')->daily()->at('00:20');
