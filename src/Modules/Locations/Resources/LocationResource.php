<?php

namespace Ocpi\Modules\Locations\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Credentials\Object\PartyRole;
use Ocpi\Modules\Locations\Objects\Location;
use Ocpi\Support\Traits\RemoveEmptyField;

/** @property Location $resource */
class LocationResource extends JsonResource
{
    use RemoveEmptyField;
    public function __construct(Location $location)
    {
        parent::__construct($location);
    }

    public function toArray(?Request $request = null): array
    {
        /** @var PartyRole $partyRole */
        $partyRole = $this->resource->getParty()->getRoles()->first();
        return self::removeEmptyField([
            'country_code' => $partyRole?->getCountryCode(),
            'party_id' => $partyRole?->getCode(),
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
            'evses' => $this->resource->getEvses() ? (new EvseResourceList($this->resource->getEvses()))->toArray() : null,
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
        ]);
    }
}