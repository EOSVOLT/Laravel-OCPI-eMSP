<?php

namespace Ocpi\Modules\Tariffs\Client\V2_2_1;

use Ocpi\Models\Party;
use Ocpi\Models\PartyToken;
use Ocpi\Support\Client\ReceiverClient;
use Ocpi\Support\Enums\Role;

class EMSPClient extends ReceiverClient
{
    public function __construct(Party $party, PartyToken $partyToken)
    {
        parent::__construct($party, $partyToken, 'tariffs', Role::EMSP);
    }
}