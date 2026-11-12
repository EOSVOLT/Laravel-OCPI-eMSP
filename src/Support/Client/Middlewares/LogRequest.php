<?php

namespace Ocpi\Support\Client\Middlewares;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ocpi\Support\Helpers\HttpLogHelper;
use Saloon\Contracts\RequestMiddleware;
use Saloon\Http\PendingRequest;
use WeakMap;

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

    /**
     * Request id and start time of every request in flight, read back by LogResponse
     * to log the response under the same id.
     *
     * @var WeakMap<PendingRequest, array{request_id: string, started_at: float}>|null
     */
    private static ?WeakMap $inFlight = null;

    public function __invoke(PendingRequest $pendingRequest): void
    {
        $requestId = Str::uuid()->toString();
        self::$inFlight ??= new WeakMap();
        self::$inFlight[$pendingRequest] = ['request_id' => $requestId, 'started_at' => microtime(true)];

        $body = $pendingRequest->body()?->isNotEmpty()
            ? json_encode($pendingRequest->body()->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : null;

        $headers = $pendingRequest->headers()->all();
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
            . HttpLogHelper::requestIdTag($requestId)
            . ($body ? PHP_EOL . $body : '')
            . $headersString,
            ['request_id' => $requestId]
        );
    }

    /**
     * @return array{request_id: string, started_at: float}|null null when the request was not logged by this middleware.
     */
    public static function inFlight(PendingRequest $pendingRequest): ?array
    {
        return self::$inFlight[$pendingRequest] ?? null;
    }
}
