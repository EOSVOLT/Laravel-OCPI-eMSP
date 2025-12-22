<?php

namespace Ocpi\Modules\Tokens\Events\EMSP;

use Illuminate\Foundation\Events\Dispatchable;

class DeletedCommandToken
{
    use Dispatchable;

    public function __construct(
        public readonly string $commandTokenId,
    ) {
    }
}