<?php

namespace Ocpi\Modules\Tariffs\Client\V2_2_1;

use Ocpi\Models\PartyToken;
use Ocpi\Support\Client\SenderClient;

class CPOClient extends SenderClient
{
    public function __construct(PartyToken $partyToken)
    {
        parent::__construct($partyToken, 'tariffs');
    }
}