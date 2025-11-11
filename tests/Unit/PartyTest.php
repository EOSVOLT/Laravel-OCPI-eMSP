<?php

namespace Tests\Unit;

use Ocpi\Modules\Credentials\Factories\PartyFactory;
use Tests\TestCase;
use Tests\Traits\SchemaTrait;
use Tests\Traits\TestLocationTrait;
use Tests\Traits\TestPartyRoleTrait;

class PartyTest extends TestCase
{
    use TestPartyRoleTrait;
    use TestLocationTrait;
    use SchemaTrait;

    public function testBuildLocationObject()
    {
        $partyMocked = $this->getMockParty(false);
        $partyObject = PartyFactory::fromModel($partyMocked);
        $this->assertEquals($partyObject->getId(), $partyMocked->id);
    }
}