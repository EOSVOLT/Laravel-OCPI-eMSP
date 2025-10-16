<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Cdrs\Server\Controllers\CPO\V2_2_1\GetController;
use Ocpi\Modules\Cdrs\Server\Controllers\CPO\V2_2_1\GetMockedController;

Route::prefix('cdrs')
    ->name('cdrs')
    ->group(function () {
        Route::get('{cdr_emsp_id?}', GetController::class)->name('.get');
        // Route only used in Versions details to give an endpoint for this Module to the CPO.
        Route::get('/', GetMockedController::class);
    });
