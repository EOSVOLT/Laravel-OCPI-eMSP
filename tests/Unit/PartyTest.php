<?php

namespace Ocpi\Tests\Unit;

use Ocpi\Modules\Credentials\Factories\PartyFactory;
use Ocpi\Tests\TestCase;
use Ocpi\Tests\Traits\SchemaTrait;
use Ocpi\Tests\Traits\TestLocationTrait;
use Ocpi\Tests\Traits\TestPartyRoleTrait;

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