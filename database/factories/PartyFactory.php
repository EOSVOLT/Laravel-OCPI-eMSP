<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Party;

class PartyFactory extends Factory
{

    protected $model = Party::class;
    public function definition(): array
    {
        return [
            'code' => 'DE*ABC',
            'parent_id' => null,
            'cpo_id' => 1,
            'url' => 'www.this_our_url.com',
            'version' => '2.2.1',
            'version_url' => 'www.their_version_url.com',
            'endpoints' => [],//a list of their endpoints
        ];
    }
}