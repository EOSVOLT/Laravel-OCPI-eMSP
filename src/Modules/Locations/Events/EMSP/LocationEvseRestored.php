<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LocationEvseRestored implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{

    use Queueable;

    public $connection = 'database';

    public $queue = 'evse:restored';

    public function __construct(
        private readonly int $evseId,
    ) {
    }

    public function getEvseId(): int
    {
        return $this->evseId;
    }
}
