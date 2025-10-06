<?php

namespace Ocpi\Modules\Tariffs\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Tariffs\Enums\TariffDimensionType;

readonly class TariffPriceComponent implements Arrayable
{

    /**
     * @param int $id
     * @param TariffDimensionType $type
     * @param float $price
     * @param int $stepSize
     * @param float|null $vat
     */
    public function __construct(
        private int $id,
        private TariffDimensionType $type,
        private float $price,
        private int $stepSize,
        private ?float $vat = null,
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