<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class LocationConnectorCreated implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    public $connection = 'database';

    public $queue = 'connector:created';

    public function __construct(
        private readonly int $connectorId,
    ) {
    }

    public function getConnectorId(): int
    {
        return $this->connectorId;
    }
}
