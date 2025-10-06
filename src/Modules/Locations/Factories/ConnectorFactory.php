<?php

namespace Ocpi\Modules\Locations\Factories;

use Illuminate\Support\Collection;
use Ocpi\Models\Locations\LocationConnector;
use Ocpi\Modules\Locations\Enums\ConnectorFormat;
use Ocpi\Modules\Locations\Enums\ConnectorType;
use Ocpi\Modules\Locations\Enums\PowerType;
use Ocpi\Modules\Locations\Objects\Connector;
use Ocpi\Modules\Locations\Objects\ConnectorCollection;

class ConnectorFactory
{
    public static function fromModel(LocationConnector $connector): Connector
    {
        $connectorObj = new Connector(
            $connector->id,
            $connector->evse_id,
            $connector->connector_id,
            ConnectorType::from($connector->object['standard']),
            ConnectorFormat::from($connector->object['format']),
            PowerType::from($connector->object['power_type']),
            $connector->object['max_voltage'],
            $connector->object['max_amperage'],
            $connector->updated_at
        );
        $connectorObj->setTariffIds($connector->tariffs->pluck('external_id')->toArray());
        return $connectorObj;
    }

    public static function fromModels(Collection $connectors): ConnectorCollection
    {
        $connectorList = new ConnectorCollection();
        foreach ($connectors as $connector) {
            $connectorList->add(self::fromModel($connector));
        }
        return $connectorList;
    }
}