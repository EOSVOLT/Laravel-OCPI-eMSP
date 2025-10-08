<?php

namespace Ocpi\Modules\Locations\Client\V2_2_1;

use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Client\Resource as OcpiResource;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Throwable;

class Resource extends OcpiResource
{
    use HandlesLocation;

    /**
     * @param string $countryCode
     * @param string $partyId
     * @param string $locationId
     * @param array $data
     * @param string|null $evseUid
     * @param string|null $connectorId
     *
     * @return array|string|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function createOrReplace(
        string $countryCode,
        string $partyId,
        string $locationId,
        array $data,
        ?string $evseUid = null,
        ?string $connectorId = null
    ): array|string|null {
        return $this->requestPutSend(
            $data,
            implode('/', [$countryCode, $partyId, $locationId]+array_filter([$evseUid, $connectorId]))
        );
    }

    /**
     * @param string $countryCode
     * @param string $partyId
     * @param string $locationId
     * @param array $data
     * @param string|null $evseUid
     * @param string|null $connectorId
     *
     * @return array|string|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function update(
        string $countryCode,
        string $partyId,
        string $locationId,
        array $data,
        ?string $evseUid = null,
        ?string $connectorId = null
    ): array|string|null {
        return $this->requestPatchSend(
            $data,
            implode('/', [$countryCode, $partyId, $locationId]+array_filter([$evseUid, $connectorId]))
        );
    }
}
