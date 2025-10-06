<?php

namespace Ocpi\Modules\Tariffs\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Tariffs\Objects\TariffElement;

class TariffElementResource extends JsonResource
{
    public function __construct(?TariffElement $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(?Request $request = null): array
    {
        return [
            'price_components' => new TariffPriceComponentResourceList($this->resource->getPriceComponents())->toArray(),
            'restrictions' => new TariffRestrictionResource($this->resource->getRestrictions())->toArray(),
        ];
    }
}