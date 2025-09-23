<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Tariffs\Server\Controllers\CPO\V2_2_1\GetController;
use Ocpi\Support\Server\Middlewares\IdentifyCPOSender;

Route::middleware([
    IdentifyCPOSender::class,
])
    ->prefix('tariffs')
    ->name('tariffs')
    ->group(function () {
        // OCPI 2.2.1: GET /tariffs and GET /tariffs/{country_code}/{party_id}/{tariff_id}
        Route::get('', [GetController::class, 'list']);
    });
