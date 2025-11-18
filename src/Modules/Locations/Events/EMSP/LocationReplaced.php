<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LocationReplaced implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly int $locationId,
    ) {
        $this->connection = 'database';
        $this->queue = 'location:replaced';
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }
}
