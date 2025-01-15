<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Commands\Server\Controllers\V2_1_1\PostController;

Route::prefix('commands')
    ->name('commands')
    ->group(function () {
        Route::post('{type}/{id?}', PostController::class)->name('.post');
    });
