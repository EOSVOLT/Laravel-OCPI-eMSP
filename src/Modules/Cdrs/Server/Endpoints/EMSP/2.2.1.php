<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Cdrs\Server\Controllers\EMSP\V2_2_1\GetController;
use Ocpi\Modules\Cdrs\Server\Controllers\EMSP\V2_2_1\PostController;

Route::prefix('cdrs')
    ->name('cdrs')
    ->group(function () {
        Route::get('/{cdr_emsp_id?}', GetController::class);
        Route::post('/', PostController::class)->name('.post');
    });
