<?php

namespace Ocpi\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ocpi\Models\Locations\LocationConnector;
use Ocpi\Models\Locations\LocationEvse;
use Tests\Traits\SchemaTrait;

class LocationConnectorFactory extends Factory
{
    use SchemaTrait;

    protected $model = LocationConnector::class;

    public function definition(): array
    {
        return [
            'evse_id' => LocationEvse::factory(),
            'connector_id' => 1,
            'object' => $this->getJsonResourceArray(__DIR__ . "/../../tests/Resources/location_connector_object.json"),
        ];
    }
}