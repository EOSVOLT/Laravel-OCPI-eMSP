<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Sessions\Server\Controllers\EMSP\V2_2_1\GetController;
use Ocpi\Modules\Sessions\Server\Controllers\EMSP\V2_2_1\PatchController;
use Ocpi\Modules\Sessions\Server\Controllers\EMSP\V2_2_1\PutController;
use Ocpi\Support\Server\Middlewares\IdentifyPartyRole;

Route::middleware([
    IdentifyPartyRole::class,
])
    ->prefix('sessions')
    ->name('sessions')
    ->group(function () {
        Route::get('{countryCode}/{partyId}/{sessionId}', GetController::class);
        Route::put('{countryCode}/{partyId}/{sessionId}', PutController::class)->name('.put');
        Route::patch('{countryCode}/{partyId}/{sessionId}', PatchController::class)->name('.patch');
    });
