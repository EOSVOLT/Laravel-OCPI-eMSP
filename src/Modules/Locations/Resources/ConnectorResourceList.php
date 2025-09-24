<?php

namespace Ocpi\Modules\Locations\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Locations\Objects\ConnectorCollection;

class ConnectorResourceList extends JsonResource
{
    public function __construct(ConnectorCollection $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(?Request $request = null): array
    {
        $data = [];
        foreach ($this->resource as $connector) {
            $data[] = new ConnectorResource($connector)->toArray();
        }
        return $data;
    }
}