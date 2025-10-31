<?php

namespace Ocpi\Modules\Locations\Client\V2_2_1;

use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Support\Client\ReceiverClient;
use Ocpi\Support\Enums\Role;

class EMSPClient extends ReceiverClient
{
    public function __construct(PartyRole $partyRole, PartyToken $partyToken)
    {
        parent::__construct($partyRole, $partyToken, 'locations');
    }
}