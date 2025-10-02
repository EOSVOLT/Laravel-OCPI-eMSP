<?php

namespace Ocpi\Modules\Sessions\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;
use Ocpi\Modules\Cdrs\DTO\CdrTokenDTO;
use Ocpi\Modules\Cdrs\DTO\ChargingPeriodDTOCollection;
use Ocpi\Support\Enums\AuthMethod;
use Ocpi\Support\Enums\SessionStatus;
use Ocpi\Support\Objects\Price;

readonly class Session implements Arrayable
{
    /**
     * @param string $id
     * @param string $countryCode
     * @param string $partyId
     * @param Carbon $startDate
     * @param Carbon|null $endDate
     * @param float $kwh
     * @param CdrTokenDTO $cdrToken
     * @param AuthMethod $authMethod
     * @param string|null $authorizationReference
     * @param string $locationId
     * @param string $evseUid
     * @param string $connectorId
     * @param string|null $meterId
     * @param string $currency
     * @param ChargingPeriodDTOCollection|null $chargingPeriods
     * @param Price $totalCost
     * @param SessionStatus $status
     * @param Carbon $lastUpdated
     */
    public function __construct(
        private string $id,
        private string $countryCode,
        private string $partyId,
        private Carbon $startDate,
        private ?Carbon $endDate = null,
        private float $kwh,
        private CdrTokenDTO $cdrToken,
        private AuthMethod $authMethod,
        private ?string $authorizationReference = null,
        private string $locationId,
        private string $evseUid,
        private string $connectorId,
        private ?string $meterId = null,
        private string $currency,
        private ?ChargingPeriodDTOCollection $chargingPeriods = null,
        private Price $totalCost,
        private SessionStatus $status,
        private Carbon $lastUpdated,
    )
    {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getPartyId(): string
    {
        return $this->partyId;
    }

    /**
     * @return Carbon
     */
    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    /**
     * @return Carbon|null
     */
    public function getEndDate(): ?Carbon
    {
        return $this->endDate;
    }

    /**
     * @return float
     */
    public function getKwh(): float
    {
        return $this->kwh;
    }

    /**
     * @return CdrTokenDTO
     */
    public function getCdrToken(): CdrTokenDTO
    {
        return $this->cdrToken;
    }

    /**
     * @return AuthMethod
     */
    public function getAuthMethod(): AuthMethod
    {
        return $this->authMethod;
    }

    /**
     * @return string|null
     */
    public function getAuthorizationReference(): ?string
    {
        return $this->authorizationReference;
    }

    /**
     * @return string
     */
    public function getLocationId(): string
    {
        return $this->locationId;
    }

    /**
     * @return string
     */
    public function getEvseUid(): string
    {
        return $this->evseUid;
    }

    /**
     * @return string
     */
    public function getConnectorId(): string
    {
        return $this->connectorId;
    }

    /**
     * @return string|null
     */
    public function getMeterId(): ?string
    {
        return $this->meterId;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return ChargingPeriodDTOCollection
     */
    public function getChargingPeriods(): ChargingPeriodDTOCollection
    {
        return $this->chargingPeriods;
    }

    /**
     * @return Price
     */
    public function getTotalCost(): Price
    {
        return $this->totalCost;
    }

    /**
     * @return SessionStatus
     */
    public function getStatus(): SessionStatus
    {
        return $this->status;
    }

    /**
     * @return Carbon
     */
    public function getLastUpdated(): Carbon
    {
        return $this->lastUpdated;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'country_code' => $this->getCountryCode(),
            'party_id' => $this->getPartyId(),
            'start_date_time' => $this->getStartDate()->toRfc3339String(),
            'end_date_time' => $this->getEndDate()->toRfc3339String(),
            'kwh' => $this->getKwh(),
            'cdr_token' => $this->getCdrToken()->toArray(),
            'auth_method' => $this->getAuthMethod()->value,
            'authorization_reference' => $this->getAuthorizationReference(),
            'location_id' => $this->getLocationId(),
            'evse_uid' => $this->getEvseUid(),
            'connector_id' => $this->getConnectorId(),
            'meter_id' => $this->getMeterId(),
            'currency' => $this->getCurrency(),
            'charging_periods' => $this->getChargingPeriods()->toArray(),
            'total_cost' => $this->getTotalCost()->toArray(),
            'status' => $this->getStatus()->value,
            'last_updated' => $this->getLastUpdated()->toRfc3339String(),
        ];
    }
}