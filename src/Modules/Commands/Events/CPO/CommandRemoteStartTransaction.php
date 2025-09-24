<?php

namespace Ocpi\Modules\Commands\Events\CPO;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Ocpi\Models\Commands\Enums\CommandType;

class CommandRemoteStartTransaction implements ShouldDispatchAfterCommit, ReceiverCommandEventInterface
{
    use Dispatchable;

    /**
     * @param string $id
     * @param CommandType $type
     */
    public function __construct(
        private readonly string $id,
        private readonly CommandType $type,
    ) {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return CommandType
     */
    public function getType(): CommandType
    {
        return $this->type;
    }
}