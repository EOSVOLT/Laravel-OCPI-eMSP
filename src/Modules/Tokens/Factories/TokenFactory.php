<?php

namespace Ocpi\Modules\Tokens\Factories;

use Illuminate\Support\Carbon;
use Ocpi\Modules\Commands\Enums\ProfileType;
use Ocpi\Modules\Commands\Enums\WhitelistType;
use Ocpi\Modules\Locations\Enums\TokenType;
use Ocpi\Modules\Tokens\Objects\Token;

class TokenFactory
{
    /**
     * @param array $data
     * @return Token
     */
    public static function fromArray(array $data): Token
    {
        return new Token(
            $data['country_code'],
            $data['party_id'],
            $data['uid'],
            TokenType::tryFrom($data['type']),
            $data['contract_id'],
            $data['visual_number'] ?? null,
            $data['issuer'],
            $data['group_id'] ?? null,
            $data['valid'],
            WhitelistType::tryFrom($data['whitelist']),
            $data['language'] ?? null,
            (true === isset($data['default_profile_type']) ? ProfileType::tryFrom(
                $data['default_profile_type']
            ) : null),
            (true === isset($data['energy_contract']) ? EnergyContractFactory::fromArray(
                $data['energy_contract']
            ) : null),
            Carbon::createFromTimeString($data['last_updated']),
        );
    }
}