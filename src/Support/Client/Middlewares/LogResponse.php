<?php

namespace Ocpi\Support\Client\Middlewares;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ocpi\Support\Helpers\HttpLogHelper;
use Saloon\Contracts\ResponseMiddleware;
use Saloon\Http\Response;

/**
 * Logs the response of an outbound OCPI request under the request id LogRequest gave it.
 */
class LogResponse implements ResponseMiddleware
{
    public function __invoke(Response $response): void
    {
        $pendingRequest = $response->getPendingRequest();
        $inFlight = LogRequest::inFlight($pendingRequest);
        $requestId = $inFlight['request_id'] ?? Str::uuid()->toString();
        $context = ['request_id' => $requestId];
        $failed = $response->failed();

        Log::channel('ocpi')->log(
            $failed ? 'error' : 'debug',
            '[OUT] RESPONSE ' . $response->status() . ' '
            . $pendingRequest->getMethod()->value . ' '
            . $pendingRequest->getUrl()
            . HttpLogHelper::requestIdTag($requestId)
            . (null !== $inFlight ? ' [' . HttpLogHelper::durationMs($inFlight['started_at']) . ' ms]' : '')
            . HttpLogHelper::body(
                $response->body(),
                (int)config('ocpi.logging.response_body_max_length', HttpLogHelper::DEFAULT_BODY_MAX_LENGTH)
            ),
            $context
        );

        if ($failed) {
            $exception = $response->toException();
            Log::channel('ocpi')->error($exception->getMessage(), $context);
            Log::channel('ocpi')->error($exception->getTraceAsString(), $context);
        }
    }
}
