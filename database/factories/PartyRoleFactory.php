<?php

namespace Ocpi\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Support\Enums\Role;

class PartyRoleFactory extends Factory
{
    protected $model = PartyRole::class;

    public function definition(): array
    {
        /** @var Party $party */
        $party = Party::factory()->create();
        $codes = explode('*', $party->code);
        return [
            'party_id' => $party->id,
            'code' => $codes[1],
            'country_code' => $codes[0],
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