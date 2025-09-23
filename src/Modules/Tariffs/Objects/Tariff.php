<?php

namespace Ocpi\Modules\Tariffs\Objects;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Locations\Objects\DisplayTextCollection;
use Ocpi\Modules\Locations\Objects\EnergyMix;
use Ocpi\Modules\Tariffs\Enums\TariffType;

class Tariff implements Arrayable
{
    /**
     * @var TariffType|null
     */
    private ?TariffType $type = null;
    /**
     * @var DisplayTextCollection|null
     */
    private ?DisplayTextCollection $tariff_alt_text = null;
    /**
     * @var string|null
     */
    private ?string $tariff_alt_url = null;
    /**
     * @var Price|null
     */
    private ?Price $minPrice = null;
    /**
     * @var Price|null
     */
    private ?Price $maxPrice = null;
    /**
     * @var Carbon|null
     */
    private ?Carbon $startDateTime = null;
    /**
     * @var Carbon|null
     */
    private ?Carbon $endDateTime = null;
    /**
     * @var EnergyMix|null
     */
    private ?EnergyMix $energyMix = null;

    /**
     * @param string $countryCode
     * @param string $party_code
     * @param string $external_id
     * @param string $currency
     * @param TariffElement $elements
     * @param Carbon $lastUpdated
     */
    public function __construct(
        private readonly string $countryCode,
        private readonly string $party_code,
        private readonly string $external_id,
        private readonly string $currency,
        private readonly TariffElement $elements,
        private readonly Carbon $lastUpdated,
    )
    {
    }

    /**
     * @return TariffType|null
     */
    public function getType(): ?TariffType
    {
        return $this->type;
    }

    /**
     * @param TariffType|null $type
     *
     * @return self
     */
    public function setType(?TariffType $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return DisplayTextCollection|null
     */
    public function getTariffAltText(): ?DisplayTextCollection
    {
        return $this->tariff_alt_text;
    }

    /**
     * @param DisplayTextCollection|null $tariff_alt_text
     *
     * @return self
     */
    public function setTariffAltText(?DisplayTextCollection $tariff_alt_text): self
    {
        $this->tariff_alt_text = $tariff_alt_text;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTariffAltUrl(): ?string
    {
        return $this->tariff_alt_url;
    }

    /**
     * @param string|null $tariff_alt_url
     *
     * @return self
     */
    public function setTariffAltUrl(?string $tariff_alt_url): self
    {
        $this->tariff_alt_url = $tariff_alt_url;
        return $this;
    }

    /**
     * @return Price|null
     */
    public function getMinPrice(): ?Price
    {
        return $this->minPrice;
    }

    /**
     * @param Price|null $minPrice
     *
     * @return self
     */
    public function setMinPrice(?Price $minPrice): self
    {
        $this->minPrice = $minPrice;
        return $this;
    }

    /**
     * @return Price|null
     */
    public function getMaxPrice(): ?Price
    {
        return $this->maxPrice;
    }

    /**
     * @param Price|null $maxPrice
     *
     * @return self
     */
    public function setMaxPrice(?Price $maxPrice): self
    {
        $this->maxPrice = $maxPrice;
        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getStartDateTime(): ?Carbon
    {
        return $this->startDateTime;
    }

    /**
     * @param Carbon|null $startDateTime
     *
     * @return self
     */
    public function setStartDateTime(?Carbon $startDateTime): self
    {
        $this->startDateTime = $startDateTime;
        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getEndDateTime(): ?Carbon
    {
        return $this->endDateTime;
    }

    /**
     * @param Carbon|null $endDateTime
     *
     * @return self
     */
    public function setEndDateTime(?Carbon $endDateTime): self
    {
        $this->endDateTime = $endDateTime;
        return $this;
    }

    /**
     * @return EnergyMix|null
     */
    public function getEnergyMix(): ?EnergyMix
    {
        return $this->energyMix;
    }

    /**
     * @param EnergyMix|null $energyMix
     *
     * @return self
     */
    public function setEnergyMix(?EnergyMix $energyMix): self
    {
        $this->energyMix = $energyMix;
        return $this;
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
    public function getPartyCode(): string
    {
        return $this->party_code;
    }

    /**
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->external_id;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return TariffElement
     */
    public function getElements(): TariffElement
    {
        return $this->elements;
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
            'country_code' => $this->countryCode,
            'party_code' => $this->party_code,
            'external_id' => $this->external_id,
            'currency' => $this->currency,
            'type' => $this->type->name,
            'tariff_alt_text' => $this->tariff_alt_text?->toArray(),
            'tariff_alt_url' => $this->tariff_alt_url,
            'min_price' => $this->minPrice?->toArray(),
            'max_price' => $this->maxPrice?->toArray(),
            'elements' => $this->elements->toArray(),
            'start_date_time' => $this->startDateTime?->toIso8601String(),
            'end_date_time' => $this->endDateTime?->toIso8601String(),
            'energy_mix' => $this->energyMix?->toArray(),
            'last_updated' => $this->lastUpdated->toISOString(),
        ];
    }
}