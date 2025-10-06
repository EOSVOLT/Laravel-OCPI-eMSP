<?php

namespace Ocpi\Modules\Locations\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Ocpi\Modules\Locations\Objects\Connector;

class LocationConnectorCreated implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        private readonly Connector $connector,
    ) {}

    public function getConnector(): Connector
    {
        return $this->connector;
    }
}
