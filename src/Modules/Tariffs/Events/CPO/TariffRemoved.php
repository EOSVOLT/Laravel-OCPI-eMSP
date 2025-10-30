<?php

namespace Ocpi\Modules\Tariffs\Events\CPO;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Ocpi\Modules\Tariffs\Objects\Tariff;

class TariffRemoved implements ShouldDispatchAfterCommit, SenderTariffEventInterface
{
    use Dispatchable;

    public function __construct(private readonly Tariff $tariff)
    {
    }

    public function getTariff(): Tariff
    {
        return $this->tariff;
    }
}