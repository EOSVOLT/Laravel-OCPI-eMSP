<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Commands\Server\Controllers\CPO\V2_2_1\PostController;
use Ocpi\Modules\Commands\Server\Controllers\CPO\V2_2_1\GetMockedController;

Route::prefix('commands')
    ->name('commands')
    ->group(function () {
        Route::post('{command}', PostController::class)->name('.post');
        // Route only used in Versions details to give an endpoint for this Module to the CPO.
        Route::get('/', GetMockedController::class);
    });
