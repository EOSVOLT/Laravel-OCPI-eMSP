<?php

namespace Ocpi\Modules\Commands\Events\CPO;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Ocpi\Models\Commands\Enums\CommandType;

class CommandRemoteStartTransaction implements ShouldDispatchAfterCommit, ReceiverCommandEventInterface
{
    use Dispatchable;

    /**
     * @param string $commandId
     */
    public function __construct(private readonly string $commandId)
    {
    }

    /**
     * @return CommandType
     */
    public function getType(): CommandType
    {
        return $this->type;
    }
}