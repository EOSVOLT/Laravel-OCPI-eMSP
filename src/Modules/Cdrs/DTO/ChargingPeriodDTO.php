<?php

namespace Ocpi\Modules\Cdrs\DTO;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;

readonly class ChargingPeriodDTO implements Arrayable
{

    /**
     * @param Carbon $startDate
     * @param CdrDimensionDTOCollection $dimensions
     * @param string $tariffId
     */
    public function __construct(
        private Carbon $startDate,
        private CdrDimensionDTOCollection $dimensions,
        private string $tariffId)
    {
    }

    /**
     * @return Carbon
     */
    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    /**
     * @return CdrDimensionDTOCollection
     */
    public function getDimensions(): CdrDimensionDTOCollection
    {
        return $this->dimensions;
    }

    /**
     * @return string
     */
    public function getTariffId(): string
    {
        return $this->tariffId;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'start_date_time' => $this->getStartDate()->toRfc3339String(),
            'dimensions' => $this->getDimensions()->toArray(),
            'tariff_id' => $this->getTariffId(),
        ];
    }
}