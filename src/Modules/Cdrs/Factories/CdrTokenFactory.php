<?php

namespace Ocpi\Modules\Cdrs\Factories;

use Ocpi\Modules\Cdrs\DTO\CdrTokenDTO;
use Ocpi\Modules\Locations\Enums\TokenType;

class CdrTokenFactory
{
    /**
     * @param array $data
     * @return CdrTokenDTO
     */
    public static function fromArray(array $data): CdrTokenDTO
    {
        return new CdrTokenDTO(
            $data['country_code'],
            $data['party_id'],
            $data['uid'],
            TokenType::tryFrom($data['type']),
            $data['contract_id']
        );
    }
}