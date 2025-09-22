<?php

namespace Ocpi\Modules\Locations\Factories;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Ocpi\Models\Locations\Location as LocationModel;
use Ocpi\Modules\Credentials\Factories\PartyFactory;
use Ocpi\Modules\Locations\Enums\ParkingType;
use Ocpi\Modules\Locations\Objects\GeoLocation;
use Ocpi\Modules\Locations\Objects\Location;
use Ocpi\Modules\Locations\Objects\LocationsCollection;

class LocationFactory
{
    /**
     * @param LocationModel $location
     *
     * @return Location
     */
    public static function fromModel(LocationModel $location): Location
    {
        $object = $location->object;
        return new Location(
            $location->id,
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
        )->setParty($location->party ? PartyFactory::fromModel($location->party) : null)
            ->setEvses($location->evses ? EvseFactory::fromCollection($location->evses) : null)
            ->setImages(ImageFactory::fromModelArray($object['images'] ?? []))
            ->setChargingWhenClosed($object['charging_when_closed'] ?? false)
            ->setName($object['name'] ?? null)
            ->setPostalCode($object['postal_code'] ?? null)
            ->setOpeningTimes(HourFactory::fromArray($object['opening_times'] ?? []))
            ->setFacilities($object['facilities'] ?? [])
            ->setParkingType(ParkingType::tryFrom($object['parking_type'] ?? ""));
    }

    /**
     * @param LengthAwarePaginator $paginator
     *
     * @return LocationsCollection
     */
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