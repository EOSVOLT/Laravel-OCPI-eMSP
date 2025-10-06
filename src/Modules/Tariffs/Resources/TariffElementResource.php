<?php

namespace Ocpi\Modules\Tariffs\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Tariffs\Objects\TariffElement;

/**
 * @property TariffElement $resource
 */
class TariffElementResource extends JsonResource
{
    public function __construct(?TariffElement $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(?Request $request = null): array
    {
        $restriction = $this->resource->getRestrictions();
        return [
            'price_components' => new TariffPriceComponentResourceList($this->resource->getPriceComponents())->toArray(),
            'restrictions' => null !== $restriction ? new TariffRestrictionResource($restriction)->toArray() : null,
        ];
    }
}