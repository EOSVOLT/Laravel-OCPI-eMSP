<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Credentials\Server\Controllers\DeleteController;
use Ocpi\Modules\Credentials\Server\Controllers\GetController;
use Ocpi\Modules\Credentials\Server\Controllers\PostController;
use Ocpi\Modules\Credentials\Server\Controllers\PutController;

Route::prefix('credentials')
    ->name('credentials')
    ->group(function () {
        Route::get('/', GetController::class);
        Route::post('/', PostController::class)->name('.post');
        Route::put('/', PutController::class)->name('.put');
        Route::delete('/', DeleteController::class)->name('.delete');
    });
