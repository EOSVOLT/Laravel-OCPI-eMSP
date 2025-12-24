<?php

namespace Ocpi\Modules\Tokens\Events\EMSP;

use Illuminate\Foundation\Events\Dispatchable;

class CreatedOrReplacedCommandTokens
{
    use Dispatchable;
    public function __construct(
        public readonly string $commandTokenId,
    ) {
    }

    public function getCommandTokenId(): string
    {
        return $this->commandTokenId;
    }
}