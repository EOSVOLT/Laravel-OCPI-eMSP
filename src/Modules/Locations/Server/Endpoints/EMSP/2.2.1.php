<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Locations\Server\Controllers\EMSP\V2_2_1\GetController;
use Ocpi\Modules\Locations\Server\Controllers\EMSP\V2_2_1\GetMockedController;
use Ocpi\Modules\Locations\Server\Controllers\EMSP\V2_2_1\PatchController;
use Ocpi\Modules\Locations\Server\Controllers\EMSP\V2_2_1\PutController;
use Ocpi\Support\Server\Middlewares\IdentifyPartyRole;

Route::middleware([
    IdentifyPartyRole::class,
])
    ->prefix('locations')
    ->name('locations')
    ->group(function () {
        Route::get('/', GetMockedController::class);
        Route::get('{countryCode}/{partyId}/{locationId}/{evseUid?}/{connectorId?}', GetController::class)->name('.get');
        Route::put('{countryCode}/{partyId}/{locationId}/{evseUid?}/{connectorId?}', PutController::class)->name('.put');
        Route::patch('{countryCode}/{partyId}/{locationId}/{evseUid?}/{connectorId?}', PatchController::class)->name('.patch');
    });
