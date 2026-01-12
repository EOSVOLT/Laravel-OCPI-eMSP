<?php

namespace Ocpi\Modules\Cdrs\Client;

use Ocpi\Support\Client\Resource as OcpiResource;
use Ocpi\Support\Objects\OCPIResponse;
use Ocpi\Support\Objects\PaginationOCPIResponse;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

class Resource extends OcpiResource
{
    /**
     * @throws FatalRequestException
     * @throws \Throwable
     * @throws RequestException
     */
    public function get(?array $query = null): OCPIResponse|PaginationOCPIResponse
    {
        return $this->requestGetSend(query: $query);
    }

    /**
     * @throws FatalRequestException
     * @throws \Throwable
     * @throws RequestException
     */
    public function post(array $payload, ?string $endpoint = null): ?array
    {
        return $this->requestPostSend($payload, $endpoint, true);
    }
}
