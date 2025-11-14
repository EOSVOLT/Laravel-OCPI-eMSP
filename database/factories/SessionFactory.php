<?php

namespace Ocpi\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Locations\LocationConnector;
use Ocpi\Models\Sessions\Session;
use Ocpi\Support\Enums\SessionStatus;

class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition(): array
    {
        /** @var LocationConnector $connector */
        $connector = LocationConnector::factory()->create();
        return [
            'party_role_id' => $connector->evse->location->party->role_cpo->id,
            'session_id' => $this->faker->uuid,
            'status' => SessionStatus::COMPLETED,
            'object' => [],
            'connector_id' => $connector->id,
            'location_id' => $connector->evse->location_id,
            'location_evse_id' => $connector->evse_id,
        ];
    }
}