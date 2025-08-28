<?php

namespace Ocpi\Modules\Locations\Object;


use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Locations\Enums\EvseStatus;

class StatusSchedule implements Arrayable
{
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
            'period_begin' => $this->getPeriodBegin()->toISOString(),
            'period_end' => $this->getPeriodEnd()?->toISOString(),
            'status' => $this->getStatus()->value,
        ];
    }
}