<?php

namespace Ocpi\Modules\Commands\Events\CPO;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class CommandRemoteStopTransaction implements ShouldDispatchAfterCommit, ReceiverCommandEventInterface
{
    use Dispatchable;

    /**
     * @param string $commandId
     * @param string $externalSessionId
     */
    public function __construct(
        private readonly string $commandId,
        private readonly string $externalSessionId,
    ) {
    }

    /**
     * @return string
     */
    public function getCommandId(): string
    {
        return $this->commandId;
    }

    /**
     * @return string
     */
    public function getExternalSessionId(): string
    {
        return $this->externalSessionId;
    }
}