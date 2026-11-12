<?php

namespace Ocpi\Support\Server\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ocpi\Support\Helpers\HttpLogHelper;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs an inbound OCPI request and its response. Both lines carry the same request id.
 * The response body is logged only when the response is a failure.
 */
class LogRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        // Not kept in the Context facade: outbound calls made while handling this request
        // overwrite shared context keys, the id must stay the same until the response is logged.
        $requestId = Str::uuid()->toString();
        $context = ['request_id' => $requestId];
        $startedAt = microtime(true);
        $target = $request->getMethod() . ' ' . $request->fullUrl() . HttpLogHelper::requestIdTag($requestId);
        $body = $request->all();

        Log::channel('ocpi')->debug(
            '[IN] ' . $target
            . (is_array($body) && count($body) > 0 ? PHP_EOL . json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : ''),
            $context
        );

        /** @var Response $response */
        $response = $next($request);

        // The body is logged for failures only: successful responses (location / EVSE lists)
        // are large, their status and duration are enough to pair them with the request.
        $status = $response->getStatusCode();
        $failed = $status >= 400;
        Log::channel('ocpi')->log(
            $failed ? 'error' : 'debug',
            '[IN] RESPONSE ' . $status . ' ' . $target
            . ' [' . HttpLogHelper::durationMs($startedAt) . ' ms]'
            . ($failed ? HttpLogHelper::body(
                $response->getContent() ?: null,
                (int)config('ocpi.logging.response_body_max_length', HttpLogHelper::DEFAULT_BODY_MAX_LENGTH)
            ) : ''),
            $context
        );

        return $response;
    }
}
