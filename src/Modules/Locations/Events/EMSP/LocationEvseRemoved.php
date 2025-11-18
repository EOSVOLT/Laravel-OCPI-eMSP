<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;

class LocationEvseRemoved implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{
    public string $connection = 'database';

    public string $queue = 'evse:removed';

    public function __construct(
        private readonly int $evseId,
    ) {
    }


    public function getEvseId(): int
    {
        return $this->evseId;
    }
}
