<?php

namespace Ocpi\Modules\Tariffs\Client\V2_2_1;

use Ocpi\Modules\Tariffs\Traits\HandlesTariff;
use Ocpi\Support\Client\Resource as OcpiResource;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Throwable;

class Resource extends OcpiResource
{
    use HandlesTariff;

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
     * @param string $countryCode
     * @param string $partyId
     * @param string $tariffId
     *
     * @return array|null
     * @throws FatalRequestException
     * @throws RequestException
     * @throws Throwable
     */
    public function tariff(
        string $countryCode,
        string $partyId,
        string $tariffId
    ): ?array {
        return $this->requestGetSend(
            implode('/', array_filter([$this->connector->resolveBaseUrl(), $countryCode, $partyId, $tariffId]))
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
                    [$this->connector->resolveBaseUrl(), $countryCode, $partyId, $tariffId]
                )
            )
        );
    }

    /**
     * @param string $countryCode
     * @param string $partyId
     * @param string $tariffId
     *
     * @return array|null
     */
    public function delete(
        string $countryCode,
        string $partyId,
        string $tariffId,
    ): ?array {
        return $this->requestDeleteSend(
            implode(
                '/',
                array_filter(
                    [$this->connector->resolveBaseUrl(), $countryCode, $partyId, $tariffId]
                )
            )
        );
    }
}
