<?php

namespace Ocpi\Modules\Locations\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Locations\Objects\Connector;
use Ocpi\Support\Traits\DateFormat;
use Ocpi\Support\Traits\RemoveEmptyField;

/**
 * @property Connector $resource
 */
class ConnectorResource extends JsonResource
{
    use RemoveEmptyField;
    use DateFormat;
    public function __construct(Connector $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(?Request $request = null): array
    {
        return self::removeEmptyField([
            'id' => $this->resource->getConnectorId(),
            'standard' => $this->resource->getStandard()->name,
            'format' => $this->resource->getFormat()->name,
            'power_type' => $this->resource->getPowerType()->name,
            'max_voltage' => $this->resource->getMaxVoltage(),
            'max_amperage' => $this->resource->getMaxAmperage(),
            'max_electric_power' => $this->resource->getMaxElectricPower(),
            'tariff_ids' => $this->resource->getTariffIds(),
            'terms_and_conditions' => $this->resource->getTermsAndConditions(),
            'last_updated' => $this->resource->getLastUpdated()->format(self::RFC3339),
        ]);
    }
}