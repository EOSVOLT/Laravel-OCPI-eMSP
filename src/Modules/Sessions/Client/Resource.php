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
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws \Throwable
     */
    public function post(array $payload): ?array
    {
        return $this->requestPostSend($payload);
    }

    /**
     * @param array $payload
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws \Throwable
     */
    public function put(array $payload): ?array
    {
        return $this->requestPutSend($payload);
    }
}
