<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Locations\Server\Controllers\CPO\V2_2_1\GetController;
use Ocpi\Support\Server\Middlewares\IdentifyParty;

Route::middleware([
    IdentifyParty::class,
])
    ->prefix('locations')
    ->name('locations')
    ->group(function () {
        Route::get('', [GetController::class, 'list']);
        Route::get('{locationId}', [GetController::class, 'location'])->name('.get-by-id');
        Route::get('{locationId}/{evseUid}', [GetController::class, 'evse'])->name('.get-by-evse-id');
        Route::get('{locationId}/{evseUid}/{connectorId}', [GetController::class, 'connector'])->name(
            '.get-by-connector-id'
        );
    });
