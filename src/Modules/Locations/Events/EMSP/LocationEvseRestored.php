<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Ocpi\Modules\Locations\Objects\Evse;

class LocationEvseRestored implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit
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
