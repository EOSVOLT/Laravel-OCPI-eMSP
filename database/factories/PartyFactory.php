<?php

namespace Ocpi\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Party;

class PartyFactory extends Factory
{

    protected $model = Party::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->countryCode() . '*' . $this->faker->unique()->ean8(),
            'parent_id' => null,
            'cpo_id' => 1,
            'version' => '2.2.1',
            'version_url' => 'www.their_version_url.com',
        ];
    }
}