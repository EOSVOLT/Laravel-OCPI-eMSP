<?php

namespace Tests\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use Ocpi\Models\Locations\Location;
use Ocpi\Models\Party;

trait TestLocationTrait
{
    use SchemaTrait;
    public function getMockLocation(Party $partyMock): Location
    {
        $locationMock = Mockery::mock(Location::class, function (MockInterface $mock) use ($partyMock) {
            $mock->shouldReceive('setAttribute')->andReturnSelf();
            $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
            $mock->shouldReceive('getAttribute')->with('external_id')->andReturn('external_id_1');
            $mock->shouldReceive('getAttribute')->with('party_id')->andReturn($partyMock->id);
            $mock->shouldReceive('getAttribute')->with('party')->andReturn($partyMock);
            $mock->shouldReceive('getAttribute')->with('object')->andReturn(
                self::getJsonResourceString(__DIR__ . '/../Resources/location_object.json')
            );
            $mock->shouldReceive('getAttribute')->with('publish')->andReturnTrue();
            $mock->shouldReceive('getAttribute')->with('evses')->andReturn(new Collection());
            $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(Carbon::now());
        });
        return $locationMock;
    }
}