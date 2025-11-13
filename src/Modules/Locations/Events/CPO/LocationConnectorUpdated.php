<?php

namespace Ocpi\Modules\Locations\Events\CPO;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Ocpi\Modules\Locations\Objects\Connector;

class LocationConnectorUpdated implements SenderLocationEventInterface, ShouldDispatchAfterCommit
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
