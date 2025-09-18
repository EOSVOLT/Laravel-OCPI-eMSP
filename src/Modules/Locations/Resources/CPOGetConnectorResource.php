<?php

namespace Ocpi\Modules\Locations\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Locations\Objects\Connector;

/**
 * @property Connector $resource
 */
class CPOGetConnectorResource extends JsonResource
{
    public function __construct(Connector $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(?Request $request = null): array
    {
        return [
            'id' => $this->resource->getConnectorId(),
            'standard' => $this->resource->getStandard()->name,
            'format' => $this->resource->getFormat()->name,
            'power_type' => $this->resource->getPowerType()->name,
            'max_voltage' => $this->resource->getMaxVoltage(),
            'max_amperage' => $this->resource->getMaxAmperage(),
            'max_electric_power' => $this->resource->getMaxElectricPower(),
            'tariff_ids' => $this->resource->getTariffIds(),
            'terms_and_conditions' => $this->resource->getTermsAndConditions(),
            'last_updated' => $this->resource->getLastUpdated()->toISOString(),
        ];
    }
}