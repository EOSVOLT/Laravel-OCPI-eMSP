<?php

namespace Ocpi\Modules\Locations\Objects;


use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Locations\Enums\EvseStatus;
use Ocpi\Support\Traits\DateFormat;

class StatusSchedule implements Arrayable
{
    use DateFormat;
    /**
     * @var Carbon|null
     */
    private ?Carbon $periodEnd = null;

    public function __construct(
        private readonly Carbon $periodBegin,
        private readonly EvseStatus $status,
    ) {
    }

    /**
     * @return Carbon|null
     */
    public function getPeriodEnd(): ?Carbon
    {
        return $this->periodEnd;
    }

    /**
     * @param Carbon|null $periodEnd
     *
     * @return $this
     */
    public function setPeriodEnd(?Carbon $periodEnd): self
    {
        $this->periodEnd = $periodEnd;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getPeriodBegin(): Carbon
    {
        return $this->periodBegin;
    }

    /**
     * @return EvseStatus
     */
    public function getStatus(): EvseStatus
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'period_begin' => $this->getPeriodBegin()->format(self::RFC3339),
            'period_end' => $this->getPeriodEnd()?->format(self::RFC3339),
            'status' => $this->getStatus()->value,
        ];
    }
}