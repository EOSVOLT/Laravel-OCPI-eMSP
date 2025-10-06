<?php

namespace Ocpi\Modules\Tariffs\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Tariffs\Enums\TariffDimensionType;

class TariffPriceComponent implements Arrayable
{
    /**
     * @param int $id
     * @param TariffDimensionType $type
     * @param float $priceExclVat
     * @param int $stepSize
     * @param float|null $vat
     * @param float|null $priceInclVat
     */
    public function __construct(
        private readonly int $id,
        private readonly TariffDimensionType $type,
        private readonly float $priceExclVat,
        private readonly int $stepSize,
        private ?float $vat = null,
        private readonly ?float $priceInclVat = null,
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

    public function getPriceExclVat(): float
    {
        return $this->priceExclVat;
    }

    public function getPriceInclVat(): ?float
    {
        return $this->priceInclVat;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType()->value,
            'price_excl_vat' => $this->getPriceExclVat(),
            'price_incl_vat' => $this->getPriceInclVat(),
            'step_size' => $this->getStepSize(),
            'vat' => $this->getVat(),
        ];
    }
}