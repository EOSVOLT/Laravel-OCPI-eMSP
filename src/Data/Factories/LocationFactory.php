<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Party;

class LocationFactory extends Factory
{

    public function definition()
    {
        return [
            'party_id' => Party::factory(),
            'external_id' => $this->faker->uuid,
            'object' => [],
            'publish' => true,
        ];
    }
}