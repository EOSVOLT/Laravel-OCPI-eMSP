<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Sessions\Server\Controllers\CPO\GetController;
use Ocpi\Modules\Sessions\Server\Controllers\CPO\PutController;
use Ocpi\Support\Server\Middlewares\IdentifyPartyRole;

Route::middleware([
    IdentifyPartyRole::class,
])
    ->prefix('sessions')
    ->name('sessions')
    ->group(function () {
        Route::get('', GetController::class);
        Route::put('{session_id}/charging_preferences', PutController::class)->name('.put');
    });
