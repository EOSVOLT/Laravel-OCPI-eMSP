<?php

namespace Ocpi\Support\Client;

use ArrayObject;
use Carbon\Carbon;
use Ocpi\Modules\Commands\Enums\CommandResponseType;
use Ocpi\Modules\Commands\Enums\CommandResultType;
use Ocpi\Support\Client\Requests\DeleteRequest;
use Ocpi\Support\Client\Requests\GetRequest;
use Ocpi\Support\Client\Requests\PatchRequest;
use Ocpi\Support\Client\Requests\PostRequest;
use Ocpi\Support\Client\Requests\PutRequest;
use Ocpi\Support\Factories\DisplayTextFactory;
use Ocpi\Support\Objects\OCPICommandResponse;
use Ocpi\Support\Objects\OCPICommandResult;
use Ocpi\Support\Objects\OCPIResponse;
use Ocpi\Support\Objects\PaginationOCPIResponse;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;
use Throwable;

class Resource extends BaseResource
{
    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function requestGetSend(
        ?string $endpoint = null,
        ?array $query = null,
    ): PaginationOCPIResponse|OCPIResponse|null {
        $response = $this->connector->send(
            (new GetRequest)
                ->withEndpoint($endpoint)
                ->withQuery($query)
        );

        $response->throw();

        return $this->responseGetProcess($response);
    }

    /**
     * @return PaginationOCPIResponse|Response|null
     */
    public function responseGetProcess(
        Response $response,
    ): PaginationOCPIResponse|OCPIResponse|null {
        if (!$response->successful()) {
            return null;
        }
        $responseArray = $response->array();
        $data = $responseArray['data'] ?? $responseArray ?? null;
        $total = $response->header('X-Total-Count') ?? 0;
        $limit = $response->header('X-Limit');
        if (null !== $limit) {
            return new PaginationOCPIResponse(
                $responseArray['status_code'],
                Carbon::parse($responseArray['timestamp']),
                $total,
                $limit,
                $data,
                $responseArray['status_message'] ?? null,
                $response->header('Link')
            );
        }

        return new OCPIResponse(
            $responseArray['status_code'],
            Carbon::parse($responseArray['timestamp']),
            $data,
            $responseArray['status_message'] ?? null,
        );
    }

    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function requestPostSend(array|ArrayObject|null $payload, ?string $endpoint = null): array|string|null
    {
        $response = $this->connector->send(
            (new PostRequest)
                ->withEndpoint($endpoint)
                ->withPayload($payload)
        );

        $response->throw();

        return $this->responsePostProcess($response);
    }

    public function responsePostProcess(Response $response): array|string|null
    {
        if (!$response->successful()) {
            return null;
        }

        $responseArray = $response->array();

        return $responseArray['data'] ?? $responseArray ?? null;
    }

    public function commandRequestSend(array|ArrayObject|null $payload, ?string $endpoint = null): OCPICommandResponse
    {
        $response = $this->connector->send(
            (new PostRequest)
                ->withEndpoint($endpoint)
                ->withPayload($payload)
        );

        $response->throw();
        $responseArray = $response->array();

        return new OCPICommandResponse(
            CommandResponseType::tryFrom($responseArray['data']['result']),
            $responseArray['data']['timeout'],
            isset($responseArray['message']) ? DisplayTextFactory::fromArrayCollection($responseArray['message']) : null
        );
    }

    public function commandResultSend(array|ArrayObject|null $payload, ?string $endpoint = null): OCPICommandResult
    {
        $response = $this->connector->send(
            (new PostRequest)
                ->withEndpoint($endpoint)
                ->withPayload($payload)
        );

        $response->throw();
        $responseArray = $response->array();

        return new OCPICommandResult(
            CommandResultType::tryFrom($responseArray['result']),
            isset($responseArray['message']) ? DisplayTextFactory::fromArrayCollection($responseArray['message']) : null
        );
    }

    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function requestPutSend(array|ArrayObject|null $payload, ?string $endpoint = null): array|string|null
    {
        $response = $this->connector->send(
            (new PutRequest)
                ->withEndpoint($endpoint)
                ->withPayload($payload)
        );

        $response->throw();

        return $this->responsePutProcess($response);
    }

    public function responsePutProcess(Response $response): array|string|null
    {
        if (!$response->successful()) {
            return null;
        }

        $responseArray = $response->array();

        return $responseArray['data'] ?? $responseArray ?? null;
    }

    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function requestPatchSend(array|ArrayObject|null $payload, ?string $endpoint = null): array|string|null
    {
        $response = $this->connector->send(
            (new PatchRequest)
                ->withEndpoint($endpoint)
                ->withPayload($payload)
        );

        $response->throw();

        return $this->responsePatchProcess($response);
    }

    public function responsePatchProcess(Response $response): array|string|null
    {
        if (!$response->successful()) {
            return null;
        }

        $responseArray = $response->array();

        return $responseArray['data'] ?? $responseArray ?? null;
    }

    public function requestDeleteSend(?string $endpoint = null): array|string|null
    {
        $response = $this->connector->send(
            (new DeleteRequest)
                ->withEndpoint($endpoint)
        );

        $response->throw();

        return $this->responseDeleteProcess($response);
    }

    public function responseDeleteProcess(Response $response): array|string|null
    {
        if (!$response->successful()) {
            return null;
        }

        $responseArray = $response->array();

        return $responseArray['data'] ?? $responseArray ?? null;
    }
}
