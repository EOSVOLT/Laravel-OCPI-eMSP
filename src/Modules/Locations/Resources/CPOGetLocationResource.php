<?php

namespace Ocpi\Modules\Locations\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Locations\Objects\Locations;

/** @property Locations $resource */
class CPOGetLocationResource extends JsonResource
{
    public function __construct(Locations $location)
    {
        parent::__construct($location);
    }

    public function toArray(?Request $request = null): array
    {
        return [
            'country_code' => $this->resource->getCountryCode(),
            'party_id' => $this->resource->getPartyId(),
            'id' => $this->resource->getExternalId(),
            'publish' => $this->resource->isPublish(),
            'publish_allowed_to ' => $this->resource->getPublishAllowedTo()?->toArray(),
            'name' => $this->resource->getName(),
            'address'=> $this->resource->getAddress(),
            'city' => $this->resource->getCity(),
            'postal_code' => $this->resource->getPostalCode(),
            'state' => $this->resource->getState(),
            'country' => $this->resource->getCountry(),
            'coordinates' => $this->resource->getCoordinates()->toArray(),
            'related_locations' => $this->resource->getRelatedLocations()?->toArray(),
            'parking_type' => $this->resource->getParkingType()?->value,
            'evses' => (new CPOGetEvseResourceList($this->resource->getEvses()))->toArray(),
            'directions' => $this->resource->getDirections()?->toArray(),
            'operator' => $this->resource->getOperator()?->toArray(),
            'suboperator' => $this->resource->getSuboperator()?->toArray(),
            'owner' => $this->resource->getOwner()?->toArray(),
            'facilities' => $this->resource->getFacilities(),
            'time_zone' => $this->resource->getTimeZone(),
            'opening_times' => $this->resource->getOpeningTimes()?->toArray(),
            'charging_when_closed' => $this->resource->isChargingWhenClosed(),
            'images' => $this->resource->getImages()?->toArray(),
            'energy_mix' => $this->resource->getEnergyMix()?->toArray(),
            'last_updated' => $this->resource->getLastUpdated()->toISOString(),
        ];
    }
}