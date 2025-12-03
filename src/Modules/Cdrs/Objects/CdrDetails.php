<?php

namespace Ocpi\Modules\Cdrs\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;
use Ocpi\Modules\Tariffs\Objects\TariffCollection;
use Ocpi\Support\Enums\AuthMethod;
use Ocpi\Support\Objects\Price;

readonly class CdrDetails implements Arrayable
{

    public function __construct(
        private string $countryCode,
        private string $partyId,
        private string $id,
        private Carbon $startTime,
        private Carbon $endTime,
        private ?string $sessionId = null,
        private CdrToken $cdrToken,
        private AuthMethod $authMethod,
        private ?string $authorizationReference = null,
        private CdrLocation $cdrLocation,
        private ?string $meterId = null,
        private string $currency,
        private ?TariffCollection $tariffs = null,
        private ChargingPeriodCollection $chargingPeriodCollection,
        private ?SignedData $signedData = null,
        private Price $totalCost,
        private ?Price $totalFixedCost = null,
        private float $totalEnergy,
        private ?Price $totalEnergyCost = null,
        private float $totalTimeHours,
        private ?Price $totalTimeCost = null,
        private float $totalParkingTimeHours,
        private ?Price $totalParkingTimeCost = null,
        private ?Price $totalReservationCost = null,
        private ?string $remarks = null,
        private ?string $invoiceReferenceId = null,
        private bool $isCredit = false,
        private ?string $creditReferenceId = null,
        private bool $isHomeChargingCompensation = false,
        private Carbon $lastUpdatedAt
    ) {
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getPartyId(): string
    {
        return $this->partyId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStartTime(): Carbon
    {
        return $this->startTime;
    }

    public function getEndTime(): Carbon
    {
        return $this->endTime;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function getCdrToken(): CdrToken
    {
        return $this->cdrToken;
    }

    public function getAuthMethod(): AuthMethod
    {
        return $this->authMethod;
    }

    public function getAuthorizationReference(): ?string
    {
        return $this->authorizationReference;
    }

    public function getCdrLocation(): CdrLocation
    {
        return $this->cdrLocation;
    }

    public function getMeterId(): ?string
    {
        return $this->meterId;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getTariffs(): TariffCollection
    {
        return $this->tariffs;
    }

    public function getChargingPeriodCollection(): ChargingPeriodCollection
    {
        return $this->chargingPeriodCollection;
    }

    public function getSignedData(): ?SignedData
    {
        return $this->signedData;
    }

    public function getTotalCost(): Price
    {
        return $this->totalCost;
    }

    public function getTotalFixedCost(): ?Price
    {
        return $this->totalFixedCost;
    }

    public function getTotalEnergy(): float
    {
        return $this->totalEnergy;
    }

    public function getTotalEnergyCost(): ?Price
    {
        return $this->totalEnergyCost;
    }

    public function getTotalTimeHours(): float
    {
        return $this->totalTimeHours;
    }

    public function getTotalTimeCost(): ?Price
    {
        return $this->totalTimeCost;
    }

    public function getTotalParkingTimeHours(): float
    {
        return $this->totalParkingTimeHours;
    }

    public function getTotalParkingTimeCost(): ?Price
    {
        return $this->totalParkingTimeCost;
    }

    public function getTotalReservationCost(): ?Price
    {
        return $this->totalReservationCost;
    }

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    public function getInvoiceReferenceId(): ?string
    {
        return $this->invoiceReferenceId;
    }

    public function isCredit(): bool
    {
        return $this->isCredit;
    }

    public function getCreditReferenceId(): ?string
    {
        return $this->creditReferenceId;
    }

    public function isHomeChargingCompensation(): bool
    {
        return $this->isHomeChargingCompensation;
    }

    public function getLastUpdatedAt(): Carbon
    {
        return $this->lastUpdatedAt;
    }

    public function toArray(): array
    {
        return [
            'country_code' => $this->getCountryCode(),
            'party_id' => $this->getPartyId(),
            'id' => $this->getId(),
            'start_date_time' => $this->getStartTime()->format('Y-m-d\TH:i:s.v\Z'),
            'end_date_time' => $this->getEndTime()->format('Y-m-d\TH:i:s.v\Z'),
            'session_id' => $this->getSessionId(),
            'cdr_token' => $this->getCdrToken()->toArray(),
            'auth_method' => $this->getAuthMethod()->value,
            'authorization_reference' => $this->getAuthorizationReference(),
            'cdr_location' => $this->getCdrLocation()->toArray(),
            'meter_id' => $this->getMeterId(),
            'currency' => $this->getCurrency(),
            'tariffs' => $this->getTariffs()->toArray(),
            'charging_periods' => $this->getChargingPeriodCollection()->toArray(),
            'signed_data' => $this->getSignedData()?->toArray(),
            'total_cost' => $this->getTotalCost()->toArray(),
            'total_fixed_cost' => $this->getTotalFixedCost()?->toArray(),
            'total_energy' => $this->getTotalEnergy(),
            'total_energy_cost' => $this->getTotalEnergyCost()?->toArray(),
            'total_time' => $this->getTotalTimeHours(),
            'total_time_cost' => $this->getTotalTimeCost()?->toArray(),
            'total_parking_time' => $this->getTotalParkingTimeHours(),
            'total_parking_cost' => $this->getTotalParkingTimeCost()?->toArray(),
            'total_reservation_cost' => $this->getTotalReservationCost()?->toArray(),
            'remarks' => $this->getRemarks(),
            'invoice_reference_id' => $this->getInvoiceReferenceId(),
            'credit' => $this->isCredit(),
            'credit_reference_id' => $this->getCreditReferenceId(),
            'home_charging_compensation' => $this->isHomeChargingCompensation(),
            'last_updated' => $this->getLastUpdatedAt()->format('Y-m-d\TH:i:s.v\Z'),
        ];
    }
}