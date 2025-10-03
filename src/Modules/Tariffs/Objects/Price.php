<?php

namespace Ocpi\Modules\Tariffs\Objects;

use Illuminate\Contracts\Support\Arrayable;

class Price implements Arrayable
{
    private ?float $incl_vat = null;
    public function __construct(
        private readonly float $excl_vat,
    )
    {
    }

    /**
     * @return float|null
     */
    public function getInclVat(): ?float
    {
        return $this->incl_vat;
    }

    /**
     * @param float|null $incl_vat
     *
     * @return self
     */
    public function setInclVat(?float $incl_vat): self
    {
        $this->incl_vat = $incl_vat;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'excl_vat' => $this->excl_vat,
            'incl_vat' => $this->incl_vat,
        ];
    }
}