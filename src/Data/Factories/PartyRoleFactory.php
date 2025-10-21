<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Party;

class PartyRoleFactory extends Factory
{

    public function definition(): array
    {
        /** @var Party $party */
        $party = Party::factory()->create(
            [
                'code' => 'DE*ABC'
            ]
        );
        return [
            'party_id' => $party->id,
            'code' => 'ABC',
            'country_code' => 'DE',
            'business_details' => [
                'name' => 'Deutsch',
                'website' => 'https://eosvolt.com',
            ]
        ];
    }
}