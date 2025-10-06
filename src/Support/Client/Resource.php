<?php

namespace Ocpi\Support\Client;

use ArrayObject;
use Ocpi\Support\Client\Requests\DeleteRequest;
use Ocpi\Support\Client\Requests\GetRequest;
use Ocpi\Support\Client\Requests\PatchRequest;
use Ocpi\Support\Client\Requests\PostRequest;
use Ocpi\Support\Client\Requests\PutRequest;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;
use Throwable;

class Resource extends BaseResource
{
    /**
     * @param string|null $endpoint
     * @param array|null $query
     *
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function requestGetSend(?string $endpoint = null, ?array $query = null): ?array
    {
        $response = $this->connector->send(
            (new GetRequest)
                ->withEndpoint($endpoint)
                ->withQuery($query)
        );

        $response->throw();

        return $this->responseGetProcess($response);
    }

    /**
     * @param array|ArrayObject|null $payload
     * @param string|null $endpoint
     *
     * @return array|string|null
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

    /**
     * @param array|ArrayObject|null $payload
     * @param string|null $endpoint
     *
     * @return array|string|null
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

    /**
     * @param array|ArrayObject|null $payload
     * @param string|null $endpoint
     *
     * @return array|string|null
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

    public function requestDeleteSend(?string $endpoint = null): array|string|null
    {
        $response = $this->connector->send(
            (new DeleteRequest)
                ->withEndpoint($endpoint)
        );

        $response->throw();

        return $this->responseDeleteProcess($response);
    }

    /**
     * @param Response $response
     *
     * @return array|null
     */
    public function responseGetProcess(Response $response): ?array
    {
        if (! $response->successful()) {
            return null;
        }

        $responseArray = $response->array();

        return $responseArray['data'] ?? null;
    }

    /**
     * @param Response $response
     *
     * @return array|string|null
     */
    public function responsePostProcess(Response $response): array|string|null
    {
        if (! $response->successful()) {
            return null;
        }

        $responseArray = $response->array();

        return $responseArray['data'] ?? null;
    }

    /**
     * @param Response $response
     *
     * @return array|string|null
     */
    public function responsePutProcess(Response $response): array|string|null
    {
        if (! $response->successful()) {
            return null;
        }

        $responseArray = $response->array();

        return $responseArray['data'] ?? null;
    }

    /**
     * @param Response $response
     * @return array|string|null
     */
    public function responsePatchProcess(Response $response): array|string|null
    {
        if (! $response->successful()) {
            return null;
        }

        $responseArray = $response->array();

        return $responseArray['data'] ?? null;
    }

    /**
     * @param Response $response
     *
     * @return array|string|null
     */
    public function responseDeleteProcess(Response $response): array|string|null
    {
        if (! $response->successful()) {
            return null;
        }

        $responseArray = $response->array();

        return $responseArray['data'] ?? null;
    }
}
