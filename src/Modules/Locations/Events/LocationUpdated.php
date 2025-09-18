<?php

namespace Ocpi\Modules\Locations\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Ocpi\Models\Locations\Location;

class LocationUpdated implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        private readonly Location $location,
    ) {}

    public function getLocation(): Location
    {
        return $this->location;
    }
}
