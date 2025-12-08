<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Sessions\Server\Controllers\CPO\V2_2_1\GetController;
use Ocpi\Modules\Sessions\Server\Controllers\CPO\V2_2_1\PutController;
use Ocpi\Support\Server\Middlewares\IdentifyParty;

Route::middleware([
    IdentifyParty::class,
])
    ->prefix('sessions')
    ->name('sessions')
    ->group(function () {
        Route::get('', GetController::class);
        Route::put('{sessionId}/charging_preferences', PutController::class)->name('.put');
    });
