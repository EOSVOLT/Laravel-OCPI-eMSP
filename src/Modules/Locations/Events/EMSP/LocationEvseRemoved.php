<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LocationEvseRemoved implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly int $evseId,
    ) {
        $this->connection = 'database';
        $this->queue = 'evse:removed';
    }


    public function getEvseId(): int
    {
        return $this->evseId;
    }
}
