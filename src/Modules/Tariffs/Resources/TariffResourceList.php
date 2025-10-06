<?php

namespace Ocpi\Modules\Tariffs\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Tariffs\Objects\TariffCollection;

class TariffResourceList extends JsonResource
{
    public function __construct(TariffCollection $resource)
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
        $collection = [];
        foreach ($this->resource as $tariff) {
            $collection[] = new TariffResource($tariff)->toArray();
        }
        return $collection;
    }
}