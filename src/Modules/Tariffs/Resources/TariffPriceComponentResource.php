<?php

namespace Ocpi\Modules\Tariffs\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;
use Ocpi\Modules\Tariffs\Objects\TariffPriceComponent;
use Ocpi\Support\Traits\RemoveEmptyField;

/**
 * @property TariffPriceComponent $resource
 */
class TariffPriceComponentResource extends JsonResource
{
    use RemoveEmptyField;
    public function __construct(TariffPriceComponent $resource)
    {
        parent::__construct($resource);
    }

    /**
     * @param Request|null $request
     *
     * @return array
     */
    #[ArrayShape(['type' => "string", 'price' => "float", 'vat' => "float|null", 'step_size' => "int"])]
    public function toArray(?Request $request = null): array
    {
        return self::removeEmptyField([
            'type' => $this->resource->getType()->value,
            'price' => $this->resource->getPriceExclVat(),
            'vat' => $this->resource->getVat(),
            'step_size' => $this->resource->getStepSize(),
        ]);
    }
}