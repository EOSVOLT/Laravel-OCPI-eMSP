<?php

namespace Ocpi\Modules\Locations\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Locations\Objects\LocationsCollection;

/**
 * @property LocationsCollection $resource
 */
class LocationResourceList extends JsonResource
{
    public function __construct(LocationsCollection $resource)
    {
        parent::__construct($resource);
    }

    /**
     * @param Request|null $request
     *
     * @return array
     */
    public function toArray(?Request $request = null): array
    {
        $data = [];
        foreach ($this->resource as $location) {
            $data[] = new LocationResource($location)->toArray();
        }
        return $data;
    }
}