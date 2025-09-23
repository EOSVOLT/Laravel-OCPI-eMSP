<?php

namespace Ocpi\Modules\Tariffs\Objects;

use Illuminate\Contracts\Support\Arrayable;

class TariffElement implements Arrayable
{
    private TariffRestrictions $restrictions;
    public function __construct(
        private readonly PriceComponentCollection $priceComponents,
    )
    {
    }

    public function getPriceComponents(): PriceComponentCollection
    {
        return $this->priceComponents;
    }

    public function getRestrictions(): TariffRestrictions
    {
        return $this->restrictions;
    }

    public function setRestrictions(TariffRestrictions $restrictions): self
    {
        $this->restrictions = $restrictions;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'price_components' => $this->priceComponents->toArray(),
            'restrictions' => $this->restrictions->toArray(),
        ];
    }
}