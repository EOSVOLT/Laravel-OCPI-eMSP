<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;

class PartyRoleFactory extends Factory
{
    protected $model = PartyRole::class;

    public function definition(): array
    {
        return [
            'party_id' =>  Party::factory(),
            'code' => 'ABC',
            'country_code' => 'DE',
            'business_details' => [
                'name' => 'Deutsch',
                'website' => 'https://eosvolt.com',
            ],
        ];
    }
}