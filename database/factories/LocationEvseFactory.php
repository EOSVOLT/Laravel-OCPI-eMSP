<?php

namespace Ocpi\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Locations\Location;
use Ocpi\Models\Locations\LocationEvse;
use Ocpi\Modules\Locations\Enums\EvseStatus;
use Ocpi\Tests\Traits\SchemaTrait;

class LocationEvseFactory extends Factory
{
    use SchemaTrait;

    protected $model = LocationEvse::class;
    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'uid' => $this->faker->uuid,
            'object' => $this->getJsonResourceArray(__DIR__ . "/../../tests/Resources/location_evse_object.json"),
            'status' => EvseStatus::AVAILABLE
        ];
    }
}