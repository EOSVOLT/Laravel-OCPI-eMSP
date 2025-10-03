<?php

namespace Ocpi\Modules\Tariffs\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Tariffs\Enums\TariffDimensionType;

class TariffPriceComponent implements Arrayable
{
    /**
     * @var float|null
     */
    private ?float $vat = null;

    /**
     * @param int $id
     * @param TariffDimensionType $type
     * @param float $price
     * @param int $stepSize
     */
    public function __construct(
        private readonly int $id,
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
            'type' => $this->getType()->value,
            'price' => $this->getPrice(),
            'step_size' => $this->getStepSize(),
            'vat' => $this->getVat(),
        ];
    }
}