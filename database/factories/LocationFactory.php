<?php

namespace Ocpi\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Locations\Location;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Tests\Traits\SchemaTrait;

class LocationFactory extends Factory
{
    use SchemaTrait;

    protected $model = Location::class;

    public function definition(): array
    {
        /** @var PartyRole $partyRole */
        $partyRole = PartyRole::factory()->create();
        return [
            'party_id' => $partyRole->party_id,
            'external_id' => $this->faker->uuid,
            'object' => $this->getJsonResourceArray(__DIR__ . "/../../src/Tests/Resources/location_object.json"),
            'publish' => true,
        ];
    }
}