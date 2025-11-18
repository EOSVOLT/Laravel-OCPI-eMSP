<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LocationReplaced implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{

    use Queueable;

    public $connection = 'database';
    public $queue = 'location:replaced';

    public function __construct(
        private readonly int $locationId,
    ) {
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }
}
