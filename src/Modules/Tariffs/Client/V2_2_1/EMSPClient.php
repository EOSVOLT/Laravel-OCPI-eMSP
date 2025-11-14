<?php

namespace Ocpi\Modules\Tariffs\Client\V2_2_1;

use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Support\Client\ReceiverClient;

class EMSPClient extends ReceiverClient
{
    public function __construct(PartyToken $partyToken)
    {
        parent::__construct($partyToken, 'tariffs');
    }
}