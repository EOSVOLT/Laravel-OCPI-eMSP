<?php

namespace Ocpi\Modules\Tariffs\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Tariffs\Objects\Tariff;
use Ocpi\Support\Traits\RemoveEmptyField;

/**
 * @mixin Tariff
 */
class TariffResource extends JsonResource
{
    use RemoveEmptyField;

    /**
     * @param Tariff $resource
     */
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
            'country_code' => $this->getCountryCode(),
            'party_id' => $this->getPartyCode(),
            'id' => $this->getExternalId(),
            'currency' => $this->getCurrency(),
            'type' => $this->getType()?->value,
            'tariff_alt_text' => $this->getTariffAltText()?->toArray(),
            'tariff_alt_url' => $this->getTariffAltUrl(),
            'min_price' => $this->getMinPrice()?->toArray(),
            'max_price' => $this->getMaxPrice()?->toArray(),
            'elements' => new TariffElementResourceList($this->getElements())->toArray(),
            'start_date_time' => $this->getStartDateTime()?->toISOString(),
            'end_date_time' => $this->getEndDateTime()?->toISOString(),
            'energy_mix' => $this->getEnergyMix()?->toArray(),
            'last_updated' => $this->getLastUpdated()->toISOString(),
        ]);
    }
}