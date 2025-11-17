<?php

namespace Ocpi\Modules\Locations\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class LocationEvseReplaced implements ReceiverLocationEventInterface, ShouldDispatchAfterCommit, ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    public $connection = 'database';

    public $queue = 'evse:replaced';

    public function __construct(
        private readonly int $evseId,
    ) {
    }

    public function getEvseId(): int
    {
        return $this->evseId;
    }
}
