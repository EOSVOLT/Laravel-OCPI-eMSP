<?php

namespace Ocpi\Modules\Locations\Client\V2_2_1;

use Illuminate\Support\Carbon;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Client\Resource as OcpiResource;
use Ocpi\Support\Objects\OCPIResponse;
use Ocpi\Support\Objects\PaginationOCPIResponse;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Throwable;

class Resource extends OcpiResource
{
    use HandlesLocation;

    /**
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
            implode('/', array_filter([$countryCode, $partyId, $locationId, $evseUid, $connectorId]))
        );
    }

    /**
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
            implode('/', array_filter([$countryCode, $partyId, $locationId, $evseUid, $connectorId]))
        );
    }

    /**
     * @throws FatalRequestException
     * @throws Throwable
     * @throws RequestException
     */
    public function get(
        ?Carbon $dateFrom = null,
        ?Carbon $dateTo = null,
        ?int $offset = null,
        ?int $limit = null
    ): ?PaginationOCPIResponse {
        $query = array_filter([
            'date_from' => $dateFrom?->format('Y-m-d'),
            'date_to' => $dateTo?->format('Y-m-d'),
            'offset' => $offset,
            'limit' => $limit,
        ], function ($value) {
            return $value !== null;
        });

        return $this->requestGetSend(
            query: $query
        );
    }

    /**
     * @throws FatalRequestException
     * @throws Throwable
     * @throws RequestException
     */
    public function location(
        string $locationId,
        ?string $evseUid = null,
        ?string $connectorId = null
    ): ?OCPIResponse {
        return $this->requestGetSend(
            implode('/', array_filter([$locationId, $evseUid, $connectorId]))
        );
    }
}
