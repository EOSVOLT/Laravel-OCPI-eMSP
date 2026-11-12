<?php

namespace Ocpi\Support\Helpers;

class HttpLogHelper
{
    public const int DEFAULT_BODY_MAX_LENGTH = 10000;

    /**
     * Tag appended to the first line of a request log and of its response log,
     * so the two lines can be matched with a single search.
     */
    public static function requestIdTag(string $requestId): string
    {
        return ' [request_id: ' . $requestId . ']';
    }

    /**
     * Format a request / response body for the log: pretty printed when it is JSON,
     * cut at $maxLength characters (0 = no limit). Returns an empty string for an empty body,
     * otherwise the body prefixed by a new line.
     */
    public static function body(array|string|null $body, int $maxLength = self::DEFAULT_BODY_MAX_LENGTH): string
    {
        if (true === is_string($body)) {
            $decoded = json_decode($body, true);
            $body = true === is_array($decoded) ? $decoded : trim($body);
        }
        if (null === $body || '' === $body || [] === $body) {
            return '';
        }
        if (true === is_array($body)) {
            $body = json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        $length = mb_strlen($body);
        if ($maxLength > 0 && $length > $maxLength) {
            $body = mb_substr($body, 0, $maxLength) . PHP_EOL . '... [truncated, ' . $length . ' characters in total]';
        }

        return PHP_EOL . $body;
    }

    public static function durationMs(float $startedAt): int
    {
        return (int)round((microtime(true) - $startedAt) * 1000);
    }
}
