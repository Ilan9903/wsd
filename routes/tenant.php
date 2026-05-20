<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthGoogleController;
use App\Http\Controllers\Auth\AuthMicrosoftController;
use App\Http\Controllers\FileDownloadController;
use App\Http\Controllers\FileUploadController;
use App\Rest\Controllers\ContactController;
use App\Rest\Controllers\FileOrFolderController;
use App\Rest\Controllers\GroupController;
use App\Rest\Controllers\LinkController;
use App\Rest\Controllers\ThemeController;
use App\Rest\Controllers\UsersController;
use Hopla\GatewayManagement\Http\GatewayController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('/api')->group(callback: function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::post('/link/download', [LinkController::class, 'linkData']);
    Route::post('/link/check-access', [LinkController::class, 'linkCheckAccess']);

    Route::get('auth/azure', [AuthMicrosoftController::class, 'redirectToMicrosoft'])->name('auth.azure');
    Route::get('azure/auth/callback', [AuthMicrosoftController::class, 'handleMicrosoftCallback'])->name('auth.azure.callback');

    Route::get('auth/google', [AuthGoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [AuthGoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    Route::prefix('2FA')->group(function () {
        Route::post('login', [AuthController::class, 'loginWith2FA'])->name('login.with.2fa');
    });

    Route::get('download/{id}', [FileDownloadController::class, 'download']);

    Route::middleware('auth:api')->group(function () {

        Route::post('refresh', [AuthController::class, 'refreshToken']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('update-logo', [ThemeController::class, 'updateLogo'])->name('update-logo');

        Rest::resource('users', UsersController::class)->withSoftDeletes();
        Rest::resource('groups', GroupController::class)->withSoftDeletes();
        Rest::resource('links', LinkController::class)->withSoftDeletes();
        Rest::resource('themes', ThemeController::class)->withSoftDeletes();
        Rest::resource('contacts', ContactController::class)->withSoftDeletes();
        Rest::resource('files-folders', FileOrFolderController::class)->withSoftDeletes();

        Route::prefix('s3/multipart')->group(function () {
            Route::post('/create', [FileUploadController::class, 'create']);
            Route::post('/sign', [FileUploadController::class, 'signPart']);
            Route::post('/complete', [FileUploadController::class, 'complete']);
            Route::post('/abort', [FileUploadController::class, 'abort']);
        });

        Route::post('webhook-link-files', [GatewayController::class, 'sendDataFileFromLink']);

        Route::post('get-user-wdp', [GatewayController::class, 'getUserWeDrop']);

    });

});
