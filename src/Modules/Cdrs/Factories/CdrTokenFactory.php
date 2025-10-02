<?php

namespace Ocpi\Modules\Cdrs\Factories;

use Ocpi\Modules\Cdrs\Objects\CdrToken;
use Ocpi\Modules\Locations\Enums\TokenType;

class CdrTokenFactory
{
    /**
     * @param array $data
     * @return CdrToken
     */
    public static function fromArray(array $data): CdrToken
    {
        return new CdrToken(
            $data['country_code'],
            $data['party_id'],
            $data['uid'],
            TokenType::tryFrom($data['type']),
            $data['contract_id']
        );
    }
}