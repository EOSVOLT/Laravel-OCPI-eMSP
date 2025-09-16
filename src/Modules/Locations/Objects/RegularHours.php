<?php

namespace Ocpi\Modules\Locations\Objects;

use Illuminate\Contracts\Support\Arrayable;

class RegularHours implements Arrayable
{
    /**
     * @param int $weekday
     * @param string $periodBegin
     * @param string $periodEnd
     */
    public function __construct(
        private readonly int $weekday,
        private readonly string $periodBegin, // time 08:00
        private readonly string $periodEnd, // time 20:00
    )
    {
    }

    /**
     * @return int
     */
    public function getWeekday(): int
    {
        return $this->weekday;
    }

    /**
     * @return string
     */
    public function getPeriodBegin(): string
    {
        return $this->periodBegin;
    }

    /**
     * @return string
     */
    public function getPeriodEnd(): string
    {
        return $this->periodEnd;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'weekday' => $this->getWeekday(),
            'period_begin' => $this->getPeriodBegin(),
            'period_end' => $this->getPeriodEnd(),
        ];
    }
}