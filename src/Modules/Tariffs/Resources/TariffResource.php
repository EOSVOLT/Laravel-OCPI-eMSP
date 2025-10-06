<?php

namespace Ocpi\Modules\Tariffs\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Tariffs\Objects\Tariff;
use Ocpi\Support\Traits\RemoveEmptyField;

/**
 * @property Tariff $resource
 */
class TariffResource extends JsonResource
{
    use RemoveEmptyField;

    public function __construct(Tariff $resource)
    {
        parent::__construct($resource);
    }

    /**
     * @param Request|null $request
     *
     * @return array
     */
    public function toArray(?Request $request = null): array
    {
        return self::removeEmptyField([
            'country_code' => $this->resource->getCountryCode(),
            'party_id' => $this->resource->getPartyCode(),
            'id' => $this->resource->getExternalId(),
            'currency' => $this->resource->getCurrency(),
            'type' => $this->resource->getType()?->value,
            'tariff_alt_text' => $this->resource->getTariffAltText()?->toArray(),
            'tariff_alt_url' => $this->resource->getTariffAltUrl(),
            'min_price' => $this->resource->getMinPrice()?->toArray(),
            'max_price' => $this->resource->getMaxPrice()?->toArray(),
            'elements' => $this->resource->getElements()->toArray(),
            'start_date_time' => $this->resource->getStartDateTime()?->toISOString(),
            'end_date_time' => $this->resource->getEndDateTime()?->toISOString(),
            'energy_mix' => $this->resource->getEnergyMix()?->toArray(),
            'last_updated' => $this->resource->getLastUpdated()->toISOString(),
        ]);
    }
}