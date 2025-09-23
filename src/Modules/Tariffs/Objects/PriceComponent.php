<?php

namespace Ocpi\Modules\Tariffs\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Tariffs\Enums\TariffDimensionType;

class PriceComponent implements Arrayable
{
    private ?float $vat = null;

    public function __construct(
        private readonly TariffDimensionType $type,
        private readonly float $price,
        private readonly int $stepSize
    ) {
    }

    /**
     * @return TariffDimensionType
     */
    public function getType(): TariffDimensionType
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getStepSize(): int
    {
        return $this->stepSize;
    }

    /**
     * @return float|null
     */
    public function getVat(): ?float
    {
        return $this->vat;
    }

    /**
     * @param float|null $vat
     *
     * @return $this
     */
    public function setVat(?float $vat): self
    {
        $this->vat = $vat;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'price' => $this->price,
            'step_size' => $this->stepSize,
            'vat' => $this->vat
        ];
    }
}