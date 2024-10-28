<?php

namespace Ocpi\Support\Client\Middlewares;

use Illuminate\Support\Facades\Log;
use Saloon\Contracts\RequestMiddleware;
use Saloon\Http\PendingRequest;

class LogRequest implements RequestMiddleware
{
    public function __invoke(PendingRequest $pendingRequest): void
    {
        $body = $pendingRequest->body()?->isNotEmpty()
            ? json_encode($pendingRequest->body()->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : null;

        Log::channel('ocpi')->debug(
            '[OUT] '.$pendingRequest->getMethod()->value.' '
            .$pendingRequest->getUrl()
            .($body ? PHP_EOL.$body : '')
        );
    }
}
