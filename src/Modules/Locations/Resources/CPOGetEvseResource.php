<?php

namespace Ocpi\Modules\Locations\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Locations\Objects\Evse;

/** @property Evse $resource */
class CPOGetEvseResource extends JsonResource
{
    public function __construct(Evse $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(?Request $request = null): array
    {
        return [
            'uid' => $this->resource->getUid(),
            'evse_id' => $this->resource->getEvseId(),
            'status' => $this->resource->getStatus()->value,
            'status_schedule' => $this->resource->getStatusScheduleCollection()?->toArray(),
            'capabilities' => $this->resource->getCapabilities()?->value,
            'connectors' => (new CPOGetConnectorResourceList($this->resource->getConnectors()))->toArray(),
            'floor_level' => $this->resource->getFloorLevel(),
            'coordinates' => $this->resource->getCoordinates()?->toArray(),
            'physical_reference' => $this->resource->getPhysicalReference(),
            'directions' => $this->resource->getDirections()?->toArray(),
            'parking_restrictions' => $this->resource->getParkingRestrictions(),
            'images' => $this->resource->getImages()?->toArray(),
            'last_updated' => $this->resource->getLastUpdated()->toISOString(),
        ];
    }
}