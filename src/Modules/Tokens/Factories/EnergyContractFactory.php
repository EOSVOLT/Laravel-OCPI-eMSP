<?php

namespace Ocpi\Modules\Tokens\Factories;

use Ocpi\Modules\Tokens\Objects\EnergyContract;

class EnergyContractFactory
{
    public static function fromArray(array $data): EnergyContract
    {
        return new EnergyContract(
            $data['supplier_name'],
            $data['contract_id'] ?? null,
        );
    }
}