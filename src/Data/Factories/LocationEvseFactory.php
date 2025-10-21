<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Locations\Location;
use Ocpi\Modules\Locations\Enums\EvseStatus;

class LocationEvseFactory extends Factory
{

    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'uid' => $this->faker->uuid,
            'object' => [],
            'status' => EvseStatus::AVAILABLE
        ];
    }
}