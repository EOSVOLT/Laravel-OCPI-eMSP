<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Support\Server\Middlewares\IdentifySenderParty;
use Ocpi\Support\Server\Middlewares\LogRequest;

Route::middleware([
    'api',
    LogRequest::class,
    IdentifySenderParty::class,
])
    ->prefix(config('ocpi.server.routing.emsp.uri_prefix'))
    ->name(config('ocpi.server.routing.emsp.name_prefix'))
    ->group(
        __DIR__ . '/../../../../Modules/Versions/Server/Endpoints/EMSP/common.php'
    );
