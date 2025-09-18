<?php

namespace Ocpi\Modules\Locations\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Locations\Objects\EvseCollection;

class EvseResourceList extends JsonResource
{
    public function __construct(EvseCollection $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request|null $request = null): array
    {
        $data = [];
        foreach ($this->resource as $evse) {
            $data[] = (new EvseResource($evse))->toArray();
        }
        return $data;
    }
}