<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Commands\Server\Controllers\CPO\V2_2_1\PostController;

Route::prefix('commands')
    ->name('commands')
    ->group(function () {
        Route::post('{command}', PostController::class);
    });
