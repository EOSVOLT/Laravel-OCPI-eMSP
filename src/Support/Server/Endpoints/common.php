<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Support\Server\Middlewares\IdentifyParty;
use Ocpi\Support\Server\Middlewares\LogRequest;

Route::middleware([
    'api',
    IdentifyParty::class,
    LogRequest::class,
])
    ->prefix('ocpi/emsp')
    ->name('ocpi.emsp.')
    ->group(
    );
