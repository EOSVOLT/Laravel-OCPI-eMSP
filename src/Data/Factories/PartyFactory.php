<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PartyFactory extends Factory
{

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