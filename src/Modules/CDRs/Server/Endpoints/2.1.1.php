<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\CDRs\Server\Controllers\V2_1_1\GetController;
use Ocpi\Modules\CDRs\Server\Controllers\V2_1_1\PostController;

Route::prefix('cdrs')
    ->name('cdrs')
    ->group(function () {
        Route::get('{cdr_emsp_id?}', GetController::class);
        Route::post('/', PostController::class)->name('.post');
    });
