<?php

namespace Ocpi\Support\Client\Middlewares;

use Illuminate\Support\Facades\Log;
use Saloon\Contracts\RequestMiddleware;
use Saloon\Http\PendingRequest;

class LogRequest implements RequestMiddleware
{
    public const array LOG_HEADERS = [
        'Content-Type',
        'Accept',
        'OCPI-to-party-id',
        'OCPI-to-country-code',
        'OCPI-from-party-id',
        'OCPI-from-country-code',
    ];

    public function __invoke(PendingRequest $pendingRequest): void
    {
        $body = $pendingRequest->body()?->isNotEmpty()
            ? json_encode($pendingRequest->body()->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : null;

        $headers = $pendingRequest->headers();
        $logHeaders = [];
        foreach (self::LOG_HEADERS as $header) {
            if (true === isset($headers[$header])) {
                $logHeaders[$header] = $headers[$header];
            }
        }
        $headersString = json_encode($logHeaders, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        Log::channel('ocpi')->debug(
            '[OUT] ' . $pendingRequest->getMethod()->value . ' '
            . $pendingRequest->getUrl()
            . ($body ? PHP_EOL . $body : '')
            . $headersString
        );
    }
}
