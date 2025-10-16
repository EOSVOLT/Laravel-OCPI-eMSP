<?php

namespace Ocpi\Modules\Cdrs\Factories;

use Ocpi\Modules\Cdrs\Objects\CdrLocation;
use Ocpi\Modules\Locations\Enums\ConnectorFormat;
use Ocpi\Modules\Locations\Enums\ConnectorType;
use Ocpi\Modules\Locations\Enums\PowerType;
use Ocpi\Modules\Locations\Objects\GeoLocation;

class CdrLocationFactory
{
    /**
     * @param array $data
     * @return CdrLocation
     */
    public static function fromArray(array $data): CdrLocation
    {
        return new CdrLocation(
            $data['id'],
            $data['name'] ?? null,
            $data['address'],
            $data['city'],
            $data['postal_code'] ?? null,
            $data['state'] ?? null,
            $data['country'],
            new GeoLocation($data['coordinates']['latitude'], $data['coordinates']['longitude']),
            $data['evse_uid'],
            $data['evse_id'],
            $data['connector_id'],
            ConnectorType::tryFrom($data['connector_standard']),
            ConnectorFormat::tryFrom($data['connector_format']),
            PowerType::tryFrom($data['connector_power_type']),
        );
    }
}