<?php

namespace Ocpi\Modules\Tariffs\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class TariffReplaced implements ShouldDispatchAfterCommit, ReceiverTariffEventInterface
{
    use Dispatchable;

    public function __construct(private readonly int $tariffId)
    {
    }

    public function getTariffId(): int
    {
        return $this->tariffId;
    }
}