<?php

namespace Ocpi\Modules\Tariffs\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Tariffs\Objects\TariffRestriction;
use Ocpi\Support\Traits\RemoveEmptyField;

/**
 * @property TariffRestriction $resource
 */
class TariffRestrictionResource extends JsonResource
{
    use RemoveEmptyField;

    public function __construct(TariffRestriction $resource)
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
            'start_time' => $this->resource->getStartTime(),
            'end_time' => $this->resource->getEndTime(),
            'start_date' => $this->resource->getStartDate(),
            'end_date' => $this->resource->getEndDate(),
            'min_kwh' => $this->resource->getMinKwh(),
            'max_kwh' => $this->resource->getMaxKwh(),
            'min_current' => $this->resource->getMinCurrent(),
            'max_current' => $this->resource->getMaxCurrent(),
            'min_power' => $this->resource->getMinPower(),
            'max_power' => $this->resource->getMaxPower(),
            'min_duration' => $this->resource->getMinDuration(),
            'max_duration' => $this->resource->getMaxDuration(),
            'day_of_week' => $this->resource->getDayOfWeek(),
        ]);
    }
}