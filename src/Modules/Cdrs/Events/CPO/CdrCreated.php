<?php

namespace Ocpi\Modules\Cdrs\Events\CPO;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class CdrCreated implements ShouldDispatchAfterCommit, SenderCdrEventInterface
{
    use Dispatchable;

    public function __construct(public int $id)
    {
    }
}
