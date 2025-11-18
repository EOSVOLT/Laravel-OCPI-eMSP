<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LocationConnectorUpdated implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly int $connectorId,
    ) {
        $this->connection = 'database';
        $this->queue = 'connector:updated';
    }


    public function getConnectorId(): int
    {
        return $this->connectorId;
    }
}
