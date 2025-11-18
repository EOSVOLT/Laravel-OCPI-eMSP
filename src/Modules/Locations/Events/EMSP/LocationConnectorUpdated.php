<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;

class LocationConnectorUpdated implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{

    

    public string $connection = 'database';

    public string $queue = 'connector:updated';

    public function __construct(
        private readonly int $connectorId,
    ) {
    }


    public function getConnectorId(): int
    {
        return $this->connectorId;
    }
}
