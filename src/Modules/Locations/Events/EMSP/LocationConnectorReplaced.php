<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LocationConnectorReplaced implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{

    use Queueable;

    public $connection = 'database';

    public $queue = 'connector:replaced';

    public function __construct(
        private readonly int $connectorId,
    ) {
    }

    public function getConnectorId(): int
    {
        return $this->connectorId;
    }
}
