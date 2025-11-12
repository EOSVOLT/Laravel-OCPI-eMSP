<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Locations\Location;
use Ocpi\Models\Party;
use Tests\Traits\SchemaTrait;

class LocationFactory extends Factory
{
    use SchemaTrait;

    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'party_id' => Party::factory(),
            'external_id' => $this->faker->uuid,
            'object' => self::getJsonResourceString(__DIR__ . "/../../tests/Resources/location.json"),
            'publish' => true,
        ];
    }
}