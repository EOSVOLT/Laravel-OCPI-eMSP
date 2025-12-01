<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Modules\Tokens\Server\Controllers\EMSP\V2_2_1\PostController;
use Ocpi\Support\Server\Middlewares\IdentifyPartyRole;

Route::middleware([
    IdentifyPartyRole::class,
])
    ->prefix('tokens')
    ->name('tokens')
    ->group(function () {
        Route::post('/{token_uid}/authorize', [PostController::class, 'authorize'])->name('.post');
    });
