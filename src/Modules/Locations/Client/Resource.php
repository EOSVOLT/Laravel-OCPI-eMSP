<?php

namespace Ocpi\Modules\Locations\Client;

use Ocpi\Support\Client\Resource as OcpiResource;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Throwable;

class Resource extends OcpiResource
{
    /**
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function all(): ?array
    {
        return $this->requestGetSend();
    }

    /**
     * @param string $locationId
     *
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function location(string $locationId): ?array
    {
        return $this->requestGetSend($locationId);
    }

    /**
     * @param string $locationId
     * @param string $evseUid
     *
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function locationEvse(string $locationId, string $evseUid): ?array
    {
        return $this->requestGetSend(implode('/', func_get_args()));
    }

    /**
     * @param string $locationId
     * @param string $evseUid
     * @param string $connectorId
     *
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function locationEvseConnector(string $locationId, string $evseUid, string $connectorId): ?array
    {
        return $this->requestGetSend(implode('/', func_get_args()));
    }

    /**
     * @param string $countryCode
     * @param string $partyId
     * @param string $locationId
     * @param array $data
     * @param string $baseEndpoint
     * @param string|null $evseUid
     * @param string|null $connectorId
     *
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function createOrReplace(
        string $countryCode,
        string $partyId,
        string $locationId,
        array $data,
        string $baseEndpoint,
        ?string $evseUid = null,
        ?string $connectorId = null
    ): ?array {
        return $this->requestPutSend(
            $data,
            implode('/', array_filter([str_replace('sender', 'receiver', $baseEndpoint),$countryCode, $partyId, $locationId, $evseUid, $connectorId]))
        );
    }

    /**
     * @param string $countryCode
     * @param string $partyId
     * @param string $locationId
     * @param array $data
     * @param string $baseEndpoint
     * @param string|null $evseUid
     * @param string|null $connectorId
     *
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function update(
        string $countryCode,
        string $partyId,
        string $locationId,
        array $data,
        string $baseEndpoint,
        ?string $evseUid = null,
        ?string $connectorId = null
    ): ?array {
        return $this->requestPatchSend(
            $data,
            implode('/', array_filter([$baseEndpoint,$countryCode, $partyId, $locationId, $evseUid, $connectorId]))
        );
    }
}
