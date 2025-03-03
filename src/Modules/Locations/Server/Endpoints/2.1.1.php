<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Locations\Server\Controllers\GetController;
use Ocpi\Modules\Locations\Server\Controllers\PatchController;
use Ocpi\Modules\Locations\Server\Controllers\PutController;
use Ocpi\Support\Server\Middlewares\IdentifyPartyRole;

Route::middleware([
    IdentifyPartyRole::class,
])
    ->prefix('locations')
    ->name('locations')
    ->group(function () {
        Route::get('{country_code?}/{party_id?}/{location_id?}/{evse_uid?}/{connector_id?}', GetController::class);
        Route::put('{country_code}/{party_id}/{location_id}/{evse_uid?}/{connector_id?}', PutController::class)->name('.put');
        Route::patch('{country_code}/{party_id}/{location_id}/{evse_uid?}/{connector_id?}', PatchController::class)->name('.patch');
    });
