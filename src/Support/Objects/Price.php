<?php

namespace Ocpi\Support\Objects;

use Illuminate\Contracts\Support\Arrayable;

readonly class Price implements Arrayable
{

    /**
     * @param float $excludeVat
     * @param float|null $includeVat
     */
    public function __construct(private float $excludeVat, private ?float $includeVat = null)
    {
    }

    /**
     * @return float
     */
    public function getExcludeVat(): float
    {
        return $this->excludeVat;
    }

    /**
     * @return float|null
     */
    public function getIncludeVat(): ?float
    {
        return $this->includeVat;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'excl_vat' => $this->getExcludeVat(),
            'incl_vat' => $this->getIncludeVat(),
        ];
    }
}