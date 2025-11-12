<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Support\Enums\Role;

class PartyRoleFactory extends Factory
{
    protected $model = PartyRole::class;

    public function definition(): array
    {
        return [
            'party_id' => Party::factory(),
            'code' => 'ABC',
            'country_code' => 'DE',
            'business_details' => [
                'name' => 'Deutsch',
                'website' => 'https://eosvolt.com',
            ],
            'role' => Role::CPO,
            'url' => 'www.this_our_url.com',
            'endpoints' => [],//a list of their endpoints
        ];
    }
}