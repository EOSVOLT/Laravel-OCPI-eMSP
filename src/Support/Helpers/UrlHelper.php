<?php

namespace Ocpi\Support\Helpers;

class UrlHelper
{
    public static function getBaseUrlByModule(string $module, string $version): string
    {
        return config('ocpi.server.routing.cpo.name_prefix') . $version . '.' . $module;
    }
}