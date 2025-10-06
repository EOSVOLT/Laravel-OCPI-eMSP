<?php

namespace Ocpi\Modules\Sessions\Client;

use Ocpi\Support\Client\Resource as OcpiResource;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

class Resource extends OcpiResource
{
    /**
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws \Throwable
     */
    public function all(): ?array
    {
        return $this->requestGetSend();
    }

    /**
     * @param array $payload
     * @param string|null $endpoint
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws \Throwable
     */
    public function post(array $payload, ?string $endpoint = null): ?array
    {
        return $this->requestPostSend($payload, $endpoint);
    }

    /**
     * @param array $payload
     * @param string|null $endpoint
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws \Throwable
     */
    public function put(array $payload, ?string $endpoint = null): ?array
    {
        return $this->requestPutSend($payload, $endpoint);
    }

    /**
     * @param array $payload
     * @param string|null $endpoint
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws \Throwable
     */
    public function patch(array $payload, ?string $endpoint = null): ?array
    {
        return $this->requestPatchSend($payload, $endpoint);
    }
}
