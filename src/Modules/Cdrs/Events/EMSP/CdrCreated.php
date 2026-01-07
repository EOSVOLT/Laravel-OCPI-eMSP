<?php

namespace Ocpi\Modules\Cdrs\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class CdrCreated implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    /**
     * @param int $id
     */
    public function __construct(private readonly int $id)
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

}
