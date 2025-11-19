<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class LocationFullyCreated implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        private readonly int $locationId,
    ) {
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }
}
