<?php

namespace Ocpi\Modules\Sessions\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class SessionCreated implements ShouldDispatchAfterCommit, ReceiverSessionEventInterface
{
    use Dispatchable;

    /**
     * @param string $id
     */
    public function __construct(
        private readonly string $id,
    ) {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

}
