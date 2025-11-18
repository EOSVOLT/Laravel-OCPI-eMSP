<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LocationConnectorReplaced implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{
    use Queueable;
    public function __construct(
        private readonly int $connectorId,
    ) {
        $this->connection = 'database';
        $this->queue = 'connector:replaced';
    }

    public function getConnectorId(): int
    {
        return $this->connectorId;
    }
}
