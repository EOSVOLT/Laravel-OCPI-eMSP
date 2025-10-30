<?php

namespace Ocpi\Modules\Tariffs\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Ocpi\Modules\Tariffs\Objects\Tariff;

class TariffCreated implements ShouldDispatchAfterCommit, ReceiverTariffEventInterface
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