<?php

namespace Ocpi\Modules\Commands\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class CommandRemoteStartTransaction implements ShouldDispatchAfterCommit, SenderCommandEventInterface
{
    use Dispatchable;

    /**
     * @param string $commandId
     */
    public function __construct(private readonly string $commandId)
    {
    }

    /**
     * @return string
     */
    public function getCommandId(): string
    {
        return $this->commandId;
    }
}