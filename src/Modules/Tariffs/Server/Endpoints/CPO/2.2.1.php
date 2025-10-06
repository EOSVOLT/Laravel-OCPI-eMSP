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
        Route::get('', [GetController::class, 'list']);
    });
