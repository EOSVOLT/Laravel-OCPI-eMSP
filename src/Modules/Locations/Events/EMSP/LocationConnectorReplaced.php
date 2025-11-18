<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;

class LocationConnectorReplaced implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{


    public string $connection = 'database';

    public string $queue = 'connector:replaced';

    public function __construct(
        private readonly int $connectorId,
    ) {
    }

    public function getConnectorId(): int
    {
        return $this->connectorId;
    }
}
