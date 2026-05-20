<?php

use App\Http\Controllers\ClientController;

Route::middleware(['api'])->group(function () {
    Route::post('/checktenant', [ClientController::class, 'checkTenant']);
    Route::post('/checkclient', [ClientController::class, 'checkGlobalClient']);
});
