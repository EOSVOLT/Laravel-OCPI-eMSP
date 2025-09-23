<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Locations\Server\Controllers\CPO\GetController;
use Ocpi\Support\Server\Middlewares\IdentifyCPOSender;

Route::middleware([
    IdentifyCPOSender::class,
])
    ->prefix('locations')
    ->name('locations')
    ->group(function () {
        Route::get('', [GetController::class, 'list']);
        Route::get('{locationId}', [GetController::class, 'location']);
        Route::get('{locationId}/{evseUid}', [GetController::class, 'evse']);
        Route::get('{locationId}/{evseUid}/{connectorId}', [GetController::class, 'connector']);
    });
