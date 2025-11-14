<?php

namespace Ocpi\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Party;
use Ocpi\Support\Helpers\GeneratorHelper;

class PartyFactory extends Factory
{

    protected $model = Party::class;

    public function definition(): array
    {
        $partyCode = GeneratorHelper::generateUniquePartyCode($this->faker->unique()->countryCode());
        return [
            'code' => $partyCode->getCodeFormatted(),
            'parent_id' => null,
            'cpo_id' => 1,
            'version' => '2.2.1',
            'version_url' => 'www.their_version_url.com',
        ];
    }
}