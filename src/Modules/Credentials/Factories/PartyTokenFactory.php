<?php

namespace Ocpi\Modules\Credentials\Factories;

use Illuminate\Support\Collection;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Credentials\Object\PartyTokenCollection;

class PartyTokenFactory
{
    /**
     * @param PartyToken $model
     *
     * @return \Ocpi\Modules\Credentials\Object\PartyToken
     */
    public static function fromModel(PartyToken $model): \Ocpi\Modules\Credentials\Object\PartyToken
    {
        return new \Ocpi\Modules\Credentials\Object\PartyToken(
            $model->id,
            $model->party_role_id,
            $model->name,
            $model->token,
            $model->registered,
        );
    }

    /**
     * @param Collection $collection
     *
     * @return PartyTokenCollection
     */
    public static function fromCollection(Collection $collection): PartyTokenCollection
    {
        $partyTokenCollection = new PartyTokenCollection();
        foreach ($collection as $model) {
            $partyTokenCollection->append(self::fromModel($model));
        }
        return $partyTokenCollection;
    }
}