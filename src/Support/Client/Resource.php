<?php

namespace Ocpi\Support\Client;

use Ocpi\Support\Client\Requests\GetRequest;
use Ocpi\Support\Client\Requests\PostRequest;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class Resource extends BaseResource
{
    public function requestGetSend(?string $endpoint = null): ?array
    {
        $response = $this->connector->send(
            (new GetRequest)
                ->endpoint($endpoint)
        );

        $response->throw();

        return $this->responseGetProcess($response);
    }

    public function requestPostSend(?array $payload): ?array
    {
        $response = $this->connector->send(
            (new PostRequest)
                ->payload($payload)
        );

        $response->throw();

        return $this->responsePostProcess($response);
    }

    public function responseGetProcess(Response $response): ?array
    {
        if (! $response->successful()) {
            return null;
        }

        $responseObject = $response->object();

        return ($responseObject?->data ?? null) ? (array) $responseObject->data : null;
    }

    public function responsePostProcess(Response $response): ?array
    {
        if (! $response->successful()) {
            return null;
        }

        $responseObject = $response->object();

        return ($responseObject?->data ?? null) ? (array) $responseObject->data : null;
    }
}
