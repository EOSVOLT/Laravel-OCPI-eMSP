<?php

namespace Ocpi\Modules\Locations\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Ocpi\Modules\Locations\Objects\Evse;
use Ocpi\Modules\Locations\Objects\Locations;

class LocationEvseRestored implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        private readonly Locations $location,
        private readonly Evse $evse,
    ) {}

    public function getEvse(): Evse
    {
        return $this->evse;
    }

    public function getLocation(): Locations
    {
        return $this->location;
    }
}
