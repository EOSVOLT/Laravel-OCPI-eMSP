<?php

namespace Tests\Unit;

use Ocpi\Models\Locations\Location;
use Ocpi\Models\Locations\LocationConnector;
use Ocpi\Models\Locations\LocationEvse;
use Ocpi\Modules\Locations\Factories\LocationFactory;
use Tests\TestCase;
use Tests\Traits\SchemaTrait;
use Tests\Traits\TestPartyRoleTrait;

class LocationTest extends TestCase
{
    use TestPartyRoleTrait;
    use SchemaTrait;

    public function testGetLocation()
    {
        $partyRole = $this->getMockPartyRole();
        /** @var Location $location */
        $location = Location::factory()->create([
            'party_id' => $partyRole->party_id,
        ]);
        /** @var LocationEvse $evse */
        $evse = LocationEvse::factory()->create([
            'location_id' => $location->id,
        ]);

        LocationConnector::factory()->create([
            'evse_id' => $evse->id,
        ]);
        $location->refresh();
        $locationObject = LocationFactory::fromModel($location);
        $this->assertEquals($locationObject->getId(), $location->id);
    }
}