<?php

namespace Ocpi\Support\Helpers;

use Illuminate\Support\Str;

class UrlHelper
{
    public static function getCPOBaseUrlByModule(string $module, string $version): string
    {
        $routeVersion = Str::replace('.', '_', $version);
        return config('ocpi.server.routing.cpo.name_prefix') . $routeVersion . '.' . $module;
    }

    public static function getEMSPBaseUrlByModule(string $module, string $version): string
    {
        $routeVersion = Str::replace('.', '_', $version);
        return config('ocpi.server.routing.emsp.name_prefix') . $routeVersion . '.' . $module;
    }
}