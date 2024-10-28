<?php

namespace Ocpi\Support\Server\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $body = $request->all();

        Log::channel('ocpi')->debug(
            '[IN] '.$request->getMethod().' '
            .$request->fullUrl()
            .(is_array($body) && count($body) > 0 ? PHP_EOL.json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '')
        );

        return $next($request);
    }
}
