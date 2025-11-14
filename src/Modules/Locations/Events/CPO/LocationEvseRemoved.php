<?php

namespace Ocpi\Modules\Locations\Events\CPO;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Ocpi\Modules\Locations\Objects\Evse;

class LocationEvseRemoved implements SenderLocationEventInterface, ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        private readonly Evse $evse,
    ) {
    }

    public function getEvse(): Evse
    {
        return $this->evse;
    }
}
