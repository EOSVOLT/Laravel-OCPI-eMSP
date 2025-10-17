<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Cdrs\Server\Controllers\CPO\V2_2_1\GetController;

Route::prefix('cdrs')
    ->name('cdrs')
    ->group(function () {
        Route::get('/', GetController::class);
    });
