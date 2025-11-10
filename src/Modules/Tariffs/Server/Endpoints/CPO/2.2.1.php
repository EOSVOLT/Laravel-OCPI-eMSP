<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Tariffs\Server\Controllers\CPO\V2_2_1\GetController;
use Ocpi\Support\Server\Middlewares\IdentifyParty;

Route::middleware([
    IdentifyParty::class,
])
    ->prefix('tariffs')
    ->name('tariffs')
    ->group(function () {
        Route::get('', [GetController::class, 'list']);
    });
