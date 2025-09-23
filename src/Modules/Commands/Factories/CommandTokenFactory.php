<?php

namespace Ocpi\Modules\Commands\Factories;

use Ocpi\Modules\Commands\Object\CommandToken;
use Ocpi\Modules\Locations\Enums\TokenType;

class CommandTokenFactory
{
    /**
     * @param array $data
     * @return CommandToken
     */
    public static function fromArray(array $data): CommandToken
    {
        return new CommandToken(
            $data['country_code'],
            $data['party_id'],
            $data['uid'],
            TokenType::tryFrom($data['type']),
            $data['contract_id'],
            $data['visual_number']
        );
    }
}