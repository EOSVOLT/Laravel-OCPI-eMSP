<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Tokens\Server\Controllers\EMSP\V2_2_1\GetController;
use Ocpi\Modules\Tokens\Server\Controllers\EMSP\V2_2_1\PostController;
use Ocpi\Support\Server\Middlewares\IdentifyParty;

Route::middleware([
    IdentifyParty::class,
])
    ->prefix('tokens')
    ->name('tokens')
    ->group(function () {
        Route::get('/', GetController::class);
        Route::post('/{token_uid}/authorize', [PostController::class, 'authorize'])->name('.post');
    });
