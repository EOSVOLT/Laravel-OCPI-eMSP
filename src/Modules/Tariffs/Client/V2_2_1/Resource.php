<?php

namespace Ocpi\Modules\Tariffs\Client\V2_2_1;

use Illuminate\Support\Carbon;
use Ocpi\Modules\Tariffs\Traits\HandlesTariff;
use Ocpi\Support\Client\Resource as OcpiResource;
use Ocpi\Support\Objects\OCPIResponse;
use Ocpi\Support\Objects\PaginationOCPIResponse;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Throwable;

class Resource extends OcpiResource
{
    use HandlesTariff;

    /**
     * @param string $countryCode
     * @param string $partyId
     * @param string $tariffId
     *
     * @return OCPIResponse|PaginationOCPIResponse
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function tariff(
        string $countryCode,
        string $partyId,
        string $tariffId
    ): OCPIResponse|PaginationOCPIResponse {
        return $this->requestGetSend(
            implode('/', array_filter([$countryCode, $partyId, $tariffId]))
        );
    }

    /**
     * @param string $countryCode
     * @param string $partyId
     * @param string $tariffId
     * @param array $data
     *
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function createOrReplace(
        string $countryCode,
        string $partyId,
        string $tariffId,
        array $data,
    ): ?array {
        return $this->requestPutSend(
            $data,
            implode(
                '/',
                array_filter(
                    [$countryCode, $partyId, $tariffId]
                )
            )
        );
    }

    /**
     * @param string $countryCode
     * @param string $partyId
     * @param string $tariffId
     *
     * @return array|string|null
     */
    public function delete(
        string $countryCode,
        string $partyId,
        string $tariffId,
    ): array|string|null {
        return $this->requestDeleteSend(
            implode(
                '/',
                array_filter(
                    [$countryCode, $partyId, $tariffId]
                )
            )
        );
    }

    /**
     * @param Carbon|null $dateFrom
     * @param Carbon|null $dateTo
     * @param int|null $offset
     * @param int|null $limit
     *
     * @return PaginationOCPIResponse|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
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
}
