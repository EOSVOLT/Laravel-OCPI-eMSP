<?php

namespace Ocpi\Modules\Versions\Client;

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
    public function information(): ?array
    {
        return $this->requestGetSend()->getData();
    }

    /**
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws \Throwable
     */
    public function details(): ?array
    {
        return $this->requestGetSend()->getData();
    }
}
