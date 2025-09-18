<?php

namespace Ocpi\Modules\Locations\Factories;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Ocpi\Models\Locations\Location;
use Ocpi\Modules\Credentials\Factories\PartyFactory;
use Ocpi\Modules\Locations\Enums\ParkingType;
use Ocpi\Modules\Locations\Objects\GeoLocation;
use Ocpi\Modules\Locations\Objects\Locations;
use Ocpi\Modules\Locations\Objects\LocationsCollection;

class LocationFactory
{
    public static function fromModel(Location $location): Locations
    {
        $location->load(['evses', 'party.roles']);
        $object = $location->object;
        $locationObj = (new Locations(
            $object['country_code'],
            $location->party_id,
            $location->external_id,
            $location->publish,
            $object['address'],
            $object['city'],
            $object['country'],
            new GeoLocation($object['coordinates']['latitude'], $object['coordinates']['longitude']),
            $object['time_zone'],
            Carbon::parse($location->updated_at),
            $location->id,
        ))->setParty(PartyFactory::fromModel($location->party))
            ->setEvses(EvseFactory::fromModels($location->evses))
            ->setImages(ImageFactory::fromModelArray($object['images'] ?? []))
            ->setChargingWhenClosed($object['charging_when_closed'] ?? false)
            ->setName($object['name'] ?? null)
            ->setPostalCode($object['postal_code'] ?? null)
            ->setOpeningTimes(HourFactory::fromArray($object['opening_times'] ?? []))
            ->setFacilities($object['facilities'] ?? [])
            ->setParkingType(ParkingType::tryFrom($object['parking_type'] ?? ""));
        return $locationObj;
    }

    public static function fromPaginator(LengthAwarePaginator $paginator): LocationsCollection
    {
        $collection = new LocationsCollection();
        foreach ($paginator->items() as $location) {
            $data = self::fromModel($location);
            $collection->add($data);
        }
        return $collection;
    }
}