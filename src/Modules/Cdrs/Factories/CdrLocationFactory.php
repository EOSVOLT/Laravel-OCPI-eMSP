<?php

namespace Ocpi\Modules\Cdrs\Factories;

use Ocpi\Models\Sessions\Session;
use Ocpi\Modules\Cdrs\Objects\CdrLocation;
use Ocpi\Modules\Locations\Enums\ConnectorFormat;
use Ocpi\Modules\Locations\Enums\ConnectorType;
use Ocpi\Modules\Locations\Enums\PowerType;
use Ocpi\Modules\Locations\Factories\ConnectorFactory;
use Ocpi\Modules\Locations\Factories\EvseFactory;
use Ocpi\Modules\Locations\Factories\LocationFactory;
use Ocpi\Modules\Locations\Objects\Connector;
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

    public static function fromSessionModel(Session $sessionModel): CdrLocation
    {
        $location = LocationFactory::fromModel($sessionModel->location);
        $locationEvse = EvseFactory::fromModel($sessionModel->evse);
        /** @var Connector $connector */
        $connector = $locationEvse->getConnectors()->filter(function (Connector $connector) use ($sessionModel) {
            return $connector->getConnectorId() === $sessionModel->object['connector_id'];
        })->first();
        return new CdrLocation(
            $location->getExternalId(),
            $location->getName(),
            $location->getAddress(),
            $location->getCity(),
            $location->getPostalCode(),
            $location->getState(),
            $location->getCountry(),
            $location->getCoordinates(),
            $locationEvse->getUid(),
            $locationEvse->getEvseId(),
            $connector->getConnectorId(),
            $connector->getStandard(),
            $connector->getFormat(),
            $connector->getPowerType(),
        );
    }
}