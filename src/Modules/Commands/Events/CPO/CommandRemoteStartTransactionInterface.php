<?php

namespace Ocpi\Modules\Commands\Events\CPO;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class CommandRemoteStartTransactionInterface implements ShouldDispatchAfterCommit, ReceiverCommandEventInterface
{
    use Dispatchable;

    /**
     * @param int $partyRoleId
     * @param string $id
     * @param string $type
     */
    public function __construct(
        private readonly int $partyRoleId,
        private readonly string $id,
        private readonly string $type,
    ) {
    }

    /**
     * @return int
     */
    public function getPartyRoleId(): int
    {
        return $this->partyRoleId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

}