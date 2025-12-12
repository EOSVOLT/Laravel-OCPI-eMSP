<?php

namespace Ocpi\Support\Client\Middlewares;

use Illuminate\Support\Facades\Log;
use Saloon\Contracts\ResponseMiddleware;
use Saloon\Http\Response;

class LogResponse implements ResponseMiddleware
{
    public function __invoke(Response $response): void
    {
        if ($response->failed()) {
            $body = $response->getPendingRequest()->body()?->isNotEmpty()
                ? json_encode($response->getPendingRequest()->body()->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                : null;

            $headers = $response->getPendingRequest()->headers()->isNotEmpty()
                ? json_encode($response->getPendingRequest()->headers()->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                : '';

            Log::channel('ocpi')->error(
                '[OUT] '.$response->getPendingRequest()->getMethod()->value.' '
                .$response->getPendingRequest()->getUrl()
                .($body ? PHP_EOL.$body : '')
                .$headers
            );
            Log::channel('ocpi')->error($response->toException()->getMessage());
            Log::channel('ocpi')->error($response->toException()->getTraceAsString());
        }
    }
}
