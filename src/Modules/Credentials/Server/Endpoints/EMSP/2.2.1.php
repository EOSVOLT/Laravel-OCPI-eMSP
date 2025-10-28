<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Credentials\Server\Controllers\DeleteController;
use Ocpi\Modules\Credentials\Server\Controllers\EMSP\V2_2_1\PostController;
use Ocpi\Modules\Credentials\Server\Controllers\EMSP\V2_2_1\PutController;
use Ocpi\Modules\Credentials\Server\Controllers\GetController;

Route::prefix('credentials')
    ->name('credentials')
    ->group(function () {
        Route::get('/', GetController::class);
        Route::post('/', PostController::class)->name('.post');
        Route::put('/', PutController::class)->name('.put');
        Route::delete('/', DeleteController::class)->name('.delete');
    });
