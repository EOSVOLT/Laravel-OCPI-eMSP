<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Ocpi\Support\Server\Middlewares\IdentifyCPOVersion;
use Ocpi\Support\Server\Middlewares\IdentifyParty;
use Ocpi\Support\Server\Middlewares\LogRequest;

Route::middleware([
    'api',
    LogRequest::class,
    IdentifyParty::class,
    IdentifyCPOVersion::class,
])
    ->prefix(config('ocpi.server.routing.cpo.uri_prefix'))
    ->name(config('ocpi.server.routing.cpo.name_prefix'))
    ->group(function () {
        foreach (config('ocpi-cpo.versions', []) as $version => $versionConfiguration) {
            if (count($versionConfiguration['modules'] ?? []) > 0) {
                Route::prefix($version)
                    ->name(Str::replace('.', '_', $version) . '.')
                    ->group(function () use ($version, $versionConfiguration) {
                        Route::middleware([])
                            ->group(
                                __DIR__ . '/../../../../Modules/Versions/Server/Endpoints/CPO/' . $version . '.php'
                            );
                        foreach ($versionConfiguration['modules'] as $module) {
                            Route::middleware([])
                                ->group(
                                    __DIR__ . '/../../../../Modules/' . Str::ucfirst(
                                        $module
                                    ) . '/Server/Endpoints/CPO/' . $version . '.php'
                                );
                        }
                    });
            }
        }
    });
