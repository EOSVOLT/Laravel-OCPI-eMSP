<?php

namespace Ocpi\Modules\Sessions\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class SessionCreated implements ShouldDispatchAfterCommit, ReceiverSessionEventInterface
{
    use Dispatchable;

    /**
     * @param string $sessionId
     */
    public function __construct(
        private readonly string $sessionId,
    ) {
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

}
