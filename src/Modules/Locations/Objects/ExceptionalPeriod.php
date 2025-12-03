<?php

namespace Ocpi\Modules\Locations\Objects;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Support\Traits\DateFormat;

class ExceptionalPeriod implements Arrayable
{
    use DateFormat;
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
            'period_begin' => $this->getPeriodBegin()->format(self::RFC3339),
            'period_end' => $this->getPeriodEnd()->format(self::RFC3339),
        ];
    }
}