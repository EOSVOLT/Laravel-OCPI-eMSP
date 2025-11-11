<?php

namespace Tests\Unit;

use Ocpi\Modules\Locations\Factories\LocationFactory;
use Tests\TestCase;
use Tests\Traits\SchemaTrait;
use Tests\Traits\TestLocationTrait;
use Tests\Traits\TestPartyRoleTrait;

class LocationTest extends TestCase
{
    use TestPartyRoleTrait;
    use TestLocationTrait;
    use SchemaTrait;

    public function testBuildLocationObject()
    {
        $partyMocked = $this->getMockParty(false);
        $locationMocked = $this->getMockLocation($partyMocked);
        $locationObject = LocationFactory::fromModel($locationMocked);
        $this->assertEquals($locationObject->getId(), $locationMocked->id);
    }
}