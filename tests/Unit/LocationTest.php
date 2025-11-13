<?php

namespace Ocpi\Tests\Unit;

use Ocpi\Modules\Locations\Factories\LocationFactory;
use Ocpi\Tests\TestCase;
use Ocpi\Tests\Traits\SchemaTrait;
use Ocpi\Tests\Traits\TestLocationTrait;
use Ocpi\Tests\Traits\TestPartyRoleTrait;

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