<?php

namespace Ocpi\Modules\Sessions\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class SessionReplaced implements ShouldDispatchAfterCommit, ReceiverSessionEventInterface
{
    use Dispatchable;

    /**
     * @param int $partyRoleId
     * @param string $id
     * @param mixed $payload
     */
    public function __construct(
        private readonly int $partyRoleId,
        private readonly string $id,
        private readonly mixed $payload,
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
     * @return mixed
     */
    public function getPayload(): mixed
    {
        return $this->payload;
    }

}
