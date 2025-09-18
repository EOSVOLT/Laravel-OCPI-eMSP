<?php

namespace Ocpi\Modules\Locations\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Ocpi\Models\Locations\Location;
use Ocpi\Modules\Locations\Objects\Locations;

class LocationCreated implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        private readonly Locations $location,
    ) {}

    public function getLocation(): Locations
    {
        return $this->location;
    }
}
