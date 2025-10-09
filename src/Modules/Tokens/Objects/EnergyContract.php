<?php

namespace Ocpi\Modules\Tokens\Objects;

use Illuminate\Contracts\Support\Arrayable;

readonly class EnergyContract implements Arrayable
{

    /**
     * @param string $supplierName
     * @param string|null $contractId
     */
    public function __construct(private string $supplierName, private ?string $contractId = null)
    {
    }

    /**
     * @return string
     */
    public function getSupplierName(): string
    {
        return $this->supplierName;
    }

    /**
     * @return string|null
     */
    public function getContractId(): ?string
    {
        return $this->contractId;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return[
            'supplier_name' => $this->getSupplierName(),
            'contract_id' => $this->getContractId(),
        ];
    }
}