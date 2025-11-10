<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Ocpi\Support\Server\Middlewares\IdentifyEMSPVersion;
use Ocpi\Support\Server\Middlewares\IdentifyParty;
use Ocpi\Support\Server\Middlewares\LogRequest;

Route::middleware([
    'api',
    LogRequest::class,
    IdentifyParty::class,
    IdentifyEMSPVersion::class,
])
    ->prefix(config('ocpi.server.routing.emsp.uri_prefix'))
    ->name(config('ocpi.server.routing.emsp.name_prefix'))
    ->group(function () {
        foreach (config('ocpi-emsp.versions', []) as $version => $versionConfiguration) {
            if (count($versionConfiguration['modules'] ?? []) > 0) {
                Route::prefix($version)
                    ->name(Str::replace('.', '_', $version) . '.')
                    ->group(function () use ($version, $versionConfiguration) {
                        Route::middleware([])
                            ->group(
                                __DIR__ . '/../../../../Modules/Versions/Server/Endpoints/EMSP/' . $version . '.php'
                            );
                        foreach ($versionConfiguration['modules'] as $module) {
                            Route::middleware([])
                                ->group(
                                    __DIR__ . '/../../../../Modules/' . Str::ucfirst(
                                        $module
                                    ) . '/Server/Endpoints/EMSP/' . $version . '.php'
                                );
                        }
                    });
            }
        }
    });