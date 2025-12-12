<?php

namespace Ocpi\Modules\Tokens\Factories;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Ocpi\Models\Tokens\CommandToken;
use Ocpi\Modules\Commands\Enums\ProfileType;
use Ocpi\Modules\Commands\Enums\WhitelistType;
use Ocpi\Modules\Locations\Enums\TokenType;
use Ocpi\Modules\Tokens\Objects\CommandTokenCollection;

class CommandTokenFactory
{
    /**
     * @param array $data
     *
     * @return CommandToken
     */
    public static function fromArray(array $data): \Ocpi\Modules\Tokens\Objects\CommandToken
    {
        return new \Ocpi\Modules\Tokens\Objects\CommandToken(
            $data['country_code'],
            $data['party_id'],
            $data['uid'],
            TokenType::tryFrom($data['type']),
            $data['contract_id'],
            $data['visual_number'] ?? null,
            $data['issuer'],
            Carbon::createFromTimeString($data['last_updated']),
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
        );
    }

    public static function fromModel(CommandToken $model): \Ocpi\Modules\Tokens\Objects\CommandToken
    {
        $role = $model->party_role;
        return new \Ocpi\Modules\Tokens\Objects\CommandToken(
            $role->country_code,
            $role->code,
            $model->uid,
            $model->type,
            $model->contract_id,
            $model->visual_number,
            $model->issuer,
            Carbon::parse($model->updated_at),
            $model->group_id,
            $model->valid,
            $model->whitelist_type,
            $model->language,
            $model->default_profile_type,
            empty($model->energy_contract) ? null : EnergyContractFactory::fromArray($model->energy_contract),
        );
    }

    public static function fromCollection(Collection $collection): CommandTokenCollection
    {
        $tokenCollection = new CommandTokenCollection();
        foreach ($collection as $model) {
            $tokenCollection->append(self::fromModel($model));
        }
        return $tokenCollection;
    }
}