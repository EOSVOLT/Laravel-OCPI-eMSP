<?php

namespace Ocpi\Modules\Commands\Client\V2_2_1;

use Ocpi\Models\PartyToken;
use Ocpi\Support\Client\ReceiverClient;

class CPOClient extends ReceiverClient
{
    public function __construct(PartyToken $partyToken)
    {
        parent::__construct($partyToken, 'commands');
    }
}
