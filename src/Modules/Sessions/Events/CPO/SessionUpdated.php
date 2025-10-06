<?php

namespace Ocpi\Modules\Sessions\Events\CPO;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class SessionUpdated implements ShouldDispatchAfterCommit, SenderSessionEventInterface
{
    use Dispatchable;

    /**
     * @param string $sessionId
     * @param array $updateData
     */
    public function __construct(
        private readonly string $sessionId,
        private readonly array $updateData,
    ) {
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
