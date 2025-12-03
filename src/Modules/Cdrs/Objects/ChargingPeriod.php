<?php

namespace Ocpi\Modules\Cdrs\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;

readonly class ChargingPeriod implements Arrayable
{

    /**
     * @param Carbon $startDate
     * @param CdrDimensionCollection $dimensions
     * @param string $tariffId
     */
    public function __construct(
        private Carbon $startDate,
        private CdrDimensionCollection $dimensions,
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
     * @return CdrDimensionCollection
     */
    public function getDimensions(): CdrDimensionCollection
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
            'start_date_time' => $this->getStartDate()->format('Y-m-d\TH:i:s.v\Z'),
            'dimensions' => $this->getDimensions()->toArray(),
            'tariff_id' => $this->getTariffId(),
        ];
    }
}