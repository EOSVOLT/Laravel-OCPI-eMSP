<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;

class PartyTokenFactory extends Factory
{
    protected $model = PartyToken::class;

    public function definition(): array
    {
        return [
            'party_role_id' => PartyRole::factory(),
            'name' => 'test token',
            'token' => Str::random(32),
            'registered' => true,
        ];
    }
}