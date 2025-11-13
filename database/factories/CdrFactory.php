<?php

namespace Ocpi\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Cdrs\Cdr;
use Ocpi\Models\Sessions\Session;

class CdrFactory extends Factory
{
    protected $model = Cdr::class;
    public function definition(): array
    {
        /** @var Session $session */
        $session = Session::factory()->create();
        return [
            'party_role_id' =>$session->party_role_id,
            'cdr_id' => $this->faker->uuid,
            'object_id' => [],
            'location_id' => $session->evse->location->id,
            'location_evse_id' => $session->evse->id,
            'session_id' => $session,
        ];
    }
}