<?php

use Illuminate\Support\Facades\Route;
use Ocpi\Support\Server\Middlewares\IdentifyReceiverParty;
use Ocpi\Support\Server\Middlewares\LogRequest;

Route::middleware([
    'api',
    LogRequest::class,
    IdentifyReceiverParty::class,
])
    ->prefix(config('ocpi.server.routing.cpo.uri_prefix'))
    ->name(config('ocpi.server.routing.cpo.name_prefix'))
    ->group(
        __DIR__ . '/../../../../Modules/Versions/Server/Endpoints/CPO/common.php'
    );
