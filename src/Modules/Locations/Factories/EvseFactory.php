<?php

namespace Ocpi\Modules\Locations\Factories;

use Illuminate\Support\Collection;
use Ocpi\Models\Locations\LocationEvse;
use Ocpi\Modules\Locations\Objects\Evse;
use Ocpi\Modules\Locations\Objects\EvseCollection;

class EvseFactory
{
    public static function fromModel(LocationEvse $evse): Evse
    {
        $evse->load('connectors');
        $connectors = ConnectorFactory::fromModels($evse->connectors);
        return new Evse(
            $evse->id,
            $evse->location_id,
            $evse->uid,
            $evse->status,
            $connectors,
            $evse->updated_at,
        )
            ->setEvseId($evse->object['evse_id'] ?? null)
            ->setCapabilities($evse->object['capabilities'] ?? []);
    }

    public static function fromCollection(Collection $evses): EvseCollection
    {
        $collection = new EvseCollection();
        foreach ($evses as $evse) {
            $collection->add(self::fromModel($evse));
        }
        return $collection;
    }
}