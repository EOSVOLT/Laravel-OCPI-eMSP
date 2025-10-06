<?php

namespace Ocpi\Modules\Tariffs\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Tariffs\Objects\TariffPriceComponentCollection;

class TariffPriceComponentResourceList extends JsonResource
{
    public function __construct(TariffPriceComponentCollection $resource)
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
        foreach ($this->resource as $tariffPriceComponent) {
            $collection[] = new TariffPriceComponentResource($tariffPriceComponent)->toArray();
        }
        return $collection;
    }
}