<?php

namespace Ocpi\Support\Server\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class IdentifyEMSPVersion
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = Str::before(Str::after($request->path(), config('ocpi.server.routing.uri_prefix').'/'), '/');

        if (Str::contains($path, '.')) {
            Context::add('ocpi_version', $path);
        }

        return $next($request);
    }
}
