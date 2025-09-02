<?php

namespace Ocpi\Modules\Locations\Objects;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class ExceptionalPeriod implements Arrayable
{
    public function __construct(
        private readonly Carbon $periodBegin,
        private readonly Carbon $periodEnd,
    )
    {
    }

    public function getPeriodBegin(): Carbon
    {
        return $this->periodBegin;
    }

    public function getPeriodEnd(): Carbon
    {
        return $this->periodEnd;
    }

    public function toArray(): array
    {
        return [
            'period_begin' => $this->getPeriodBegin()->toISOString(),
            'period_end' => $this->getPeriodEnd()->toISOString(),
        ];
    }
}