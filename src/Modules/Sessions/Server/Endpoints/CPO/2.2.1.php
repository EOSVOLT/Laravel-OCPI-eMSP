<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Sessions\Server\Controllers\CPO\GetController;
use Ocpi\Modules\Sessions\Server\Controllers\CPO\PatchController;
use Ocpi\Modules\Sessions\Server\Controllers\CPO\PutController;
use Ocpi\Support\Server\Middlewares\IdentifyPartyRole;

Route::middleware([
    IdentifyPartyRole::class,
])
    ->prefix('sessions')
    ->name('sessions')
    ->group(function () {
        Route::get('{country_code?}/{party_id?}/{session_id?}', GetController::class);
        Route::put('{country_code}/{party_id}/{session_id}', PutController::class)->name('.put');
        Route::patch('{country_code}/{party_id}/{session_id}', PatchController::class)->name('.patch');
    });
