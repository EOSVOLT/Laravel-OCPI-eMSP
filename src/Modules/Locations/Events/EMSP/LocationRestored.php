<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;

class LocationRestored implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{

    

    public string $connection = 'database';
    public string $queue = 'location:restored';

    public function __construct(
        private readonly int $locationId,
    ) {
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }
}
