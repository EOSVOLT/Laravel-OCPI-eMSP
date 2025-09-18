<?php

namespace Ocpi\Modules\Locations\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Locations\Objects\LocationsCollection;

/**
 * @property LocationsCollection $resource
 */
class CPOGetLocationResourceList extends JsonResource
{
    public function __construct(LocationsCollection $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(?Request $request = null)
    {
        $data = [];
        foreach ($this->resource as $location) {
            $data[] = new CPOGetLocationResource($location);
        }
        return $data;
    }
}