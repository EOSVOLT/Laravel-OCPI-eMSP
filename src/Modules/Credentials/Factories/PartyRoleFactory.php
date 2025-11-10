<?php

namespace Ocpi\Modules\Credentials\Factories;

use Illuminate\Support\Collection;
use Ocpi\Modules\Credentials\Object\PartyRole;
use Ocpi\Modules\Credentials\Object\PartyRoleCollection;

class PartyRoleFactory
{
    /**
     * @param \Ocpi\Models\PartyRole $model
     *
     * @return PartyRole
     */
    public static function fromModel(\Ocpi\Models\PartyRole $model): PartyRole
    {
        return new PartyRole(
            $model->id,
            $model->party_id,
            $model->code,
            $model->role,
            $model->country_code,
            (array)$model->business_details ?? [],
            PartyTokenFactory::fromCollection($model->tokens),
            $model->url,
            $model->endpoints,
        );
    }

    /**
     * @param Collection $collection
     *
     * @return PartyRoleCollection
     */
    public static function fromModels(Collection $collection): PartyRoleCollection
    {
        $roles = new PartyRoleCollection();
        foreach ($collection as $role) {
            $roles->add(self::fromModel($role));
        }
        return $roles;
    }
}