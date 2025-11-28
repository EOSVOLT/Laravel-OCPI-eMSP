<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Tariffs\Server\Controllers\EMSP\V2_2_1\DeleteController;
use Ocpi\Modules\Tariffs\Server\Controllers\EMSP\V2_2_1\GetController;
use Ocpi\Modules\Tariffs\Server\Controllers\EMSP\V2_2_1\PutController;
use Ocpi\Support\Server\Middlewares\IdentifyParty;
use Ocpi\Support\Server\Middlewares\IdentifyPartyRole;

Route::middleware([
    IdentifyParty::class,
    IdentifyPartyRole::class,
])
    ->prefix('tariffs')
    ->name('tariffs')
    ->group(function () {
        Route::get('', [GetController::class, 'list']);
        Route::put('/{countryCode}/{partyId}/{externalId}', [PutController::class, 'upsert'])->name('.put');
        Route::delete('/{countryCode}/{partyId}/{externalId}', [DeleteController::class, 'delete'])->name('.delete');
    });
