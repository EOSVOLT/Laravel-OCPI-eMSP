<?php

declare(strict_types=1);

namespace Ocpi\Support\Traits\Server;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Modules\Commands\Enums\CommandResponseType;
use Ocpi\Modules\Commands\Object\CommandResponse;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Enums\OcpiServerErrorCode;
use Ocpi\Support\Helpers\UrlHelper;
use Ocpi\Support\Objects\DisplayText;
use Ocpi\Support\Traits\DateFormat;

trait Response
{
    use DateFormat;
    protected function ocpiSuccessPaginateResponse(
        array $data,
        int $offset,
        int $limit,
        int $total,
        string $module,
        $statusMessage = 'Success'
    ): JsonResponse {
        $isNextPage = (($offset + $limit) < $total);
        return $this->ocpiResponse(
            data: $data,
            httpCode: 200,
            statusCode: 1000,
            statusMessage: $statusMessage,
            paginator: [
                'link' => $isNextPage ? $this->generateNextPageLink(
                    $offset,
                    $limit,
                    $module,
                ) : null,
                'total' => $total,
                'limit' => $limit,
            ]
        );
    }

    protected function ocpiCommandAcceptedResponse(): JsonResponse
    {
        $acceptedResponse = new CommandResponse(
            CommandResponseType::ACCEPTED,
            new DisplayText('en', 'Command Accepted')
        );
        return $this->ocpiSuccessResponse($acceptedResponse->toArray());
    }

    protected function ocpiSuccessResponse(mixed $data = null, $statusMessage = 'Success'): JsonResponse
    {
        return $this->ocpiResponse(data: $data, httpCode: 200, statusCode: 1000, statusMessage: $statusMessage);
    }

    protected function ocpiCreatedResponse(mixed $data = null, $statusMessage = null): JsonResponse
    {
        return $this->ocpiResponse(data: $data, httpCode: 201, statusCode: 1000, statusMessage: $statusMessage);
    }

    protected function ocpiClientErrorResponse(
        OcpiClientErrorCode $statusCode = OcpiClientErrorCode::Generic,
        $statusMessage = 'Error',
        int $httpCode = 400
    ): JsonResponse {
        return $this->ocpiResponse(httpCode: $httpCode, statusCode: $statusCode, statusMessage: $statusMessage);
    }

    protected function ocpiServerErrorResponse(
        OcpiServerErrorCode $statusCode = OcpiServerErrorCode::Generic,
        $statusMessage = 'Error',
        int $httpCode = 400
    ): JsonResponse {
        return $this->ocpiResponse(httpCode: $httpCode, statusCode: $statusCode, statusMessage: $statusMessage);
    }

    protected function ocpiResponse(
        mixed $data = null,
        $httpCode = null,
        $statusCode = null,
        $statusMessage = null,
        array $paginator = []
    ): JsonResponse {
        $payload = collect([])
            ->when($data !== null, function ($payload) use ($data) {
                return $payload->put('data', $data);
            })
            ->put('status_code', $statusCode)
            ->when($statusMessage !== null, function ($payload) use ($statusMessage) {
                return $payload->put('status_message', $statusMessage);
            })
            ->put('timestamp', Carbon::now()->format(self::RFC3339));

        $headers = [
            'Content-Type' => 'application/json',
        ];
        if (!empty($paginator)) {
            $headers = array_merge($headers, [
                'Link' => $paginator['link'],
                'X-Total-Count' => $paginator['total'],
                'X-Limit' => $paginator['limit'],
            ]);
        }
        return response()
            ->json($payload, $httpCode)
            ->withHeaders($headers);
    }

    private function generateNextPageLink(
        int $offset,
        int $limit,
        string $module,
    ): string {
        $nextOffset = $offset + $limit;
        $query = [];
        $query['offset'] = $nextOffset;
        $query['limit'] = $limit;
        $query = array_merge(Request::capture()->query->all(), $query);
        $route = UrlHelper::getCPOBaseUrlByModule($module, Context::get('ocpi_version'));
        $basePath = route($route);
        return "<" . $basePath . '?' . http_build_query($query) . ">; rel=\"next\"";
    }
}
