<?php

namespace Ocpi\Modules\Credentials\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class CredentialsCreated implements ShouldDispatchAfterCommit, CredentialEventInterface
{
    use Dispatchable;

    public function __construct(
        private readonly int $partyId,
    ) {
    }

    public function getPartyId(): int
    {
        return $this->partyId;
    }

}
