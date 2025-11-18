<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LocationRemoved implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{

    use Queueable;
    public function __construct(
        private readonly int $locationId,
    ) {
        $this->connection = 'database';
        $this->queue = 'location:removed';
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }
}
