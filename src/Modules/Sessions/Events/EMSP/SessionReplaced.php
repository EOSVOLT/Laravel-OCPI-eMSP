<?php

namespace Ocpi\Modules\Sessions\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class SessionReplaced implements ShouldDispatchAfterCommit, ReceiverSessionEventInterface
{
    use Dispatchable;

    /**
     * @param int $partyRoleId
     * @param string $sessionId
     * @param array $updateData
     */
    public function __construct(
        private readonly int $partyRoleId,
        private readonly string $sessionId,
        private readonly array $updateData,
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
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @return array
     */
    public function getUpdateData(): array
    {
        return $this->updateData;
    }

}
