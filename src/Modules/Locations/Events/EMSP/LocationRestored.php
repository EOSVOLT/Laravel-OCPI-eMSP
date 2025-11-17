<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class LocationRestored implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    public $connection = 'database';
    public $queue = 'location:restored';

    public function __construct(
        private readonly int $locationId,
    ) {
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }
}
