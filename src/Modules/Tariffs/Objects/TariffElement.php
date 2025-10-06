<?php

namespace Ocpi\Modules\Tariffs\Objects;

use Illuminate\Contracts\Support\Arrayable;

class TariffElement implements Arrayable
{
    private ?TariffRestriction $restrictions = null;

    /**
     * @param int $id
     * @param TariffPriceComponentCollection $priceComponents
     */
    public function __construct(
        private readonly int $id,
        private readonly TariffPriceComponentCollection $priceComponents,
    ) {
    }

    /**
     * @return TariffPriceComponentCollection
     */
    public function getPriceComponents(): TariffPriceComponentCollection
    {
        return $this->priceComponents;
    }

    /**
     * @return TariffRestriction|null
     */
    public function getRestrictions(): ?TariffRestriction
    {
        return $this->restrictions;
    }

    /**
     * @param TariffRestriction $restrictions
     *
     * @return $this
     */
    public function setRestrictions(TariffRestriction $restrictions): self
    {
        $this->restrictions = $restrictions;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'price_components' => $this->getPriceComponents()->toArray(),
            'restrictions' => $this->getRestrictions()?->toArray(),
        ];
    }
}