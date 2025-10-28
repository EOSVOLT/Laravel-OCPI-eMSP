<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Commands\Server\Controllers\EMSP\V2_2_1\GetController;
use Ocpi\Modules\Commands\Server\Controllers\EMSP\V2_2_1\PostController;

Route::prefix('commands')
    ->name('commands')
    ->group(function () {
        Route::post('{type}/{id?}', PostController::class)->name('.post');
        // Route only used in Versions details to give an endpoint for this Module to the CPO.
        Route::get('/', GetController::class);
    });
