<?php

namespace Tests\Traits;

use Ocpi\Models\PartyRole;

trait TestPartyRoleTrait
{
    public function getMockPartyRole(): PartyRole
    {
        return \Mockery::mock(PartyRole::class);
    }
}