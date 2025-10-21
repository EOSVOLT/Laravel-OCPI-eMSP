<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Locations\LocationEvse;

class LocationConnectorFactory extends Factory
{

    public function definition(): array
    {
        return [
            'evse_id' => LocationEvse::factory(),
            'connector_id' => 1,
            'object' => [],
        ];
    }
}