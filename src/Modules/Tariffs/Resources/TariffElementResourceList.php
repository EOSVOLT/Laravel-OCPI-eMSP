<?php

namespace Ocpi\Modules\Tariffs\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Tariffs\Objects\TariffElementCollection;

class TariffElementResourceList extends JsonResource
{
    public function __construct(TariffElementCollection $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(?Request $request = null): array
    {
        $data = [];
        foreach ($this->resource as $tariffElement) {
            $data[] = new TariffElementResource($tariffElement)->toArray();
        }
        return $data;
    }
}