<?php

namespace Ocpi\Modules\Credentials\Factories;

use Illuminate\Support\Collection;
use Ocpi\Modules\Credentials\Object\PartyRole;
use Ocpi\Modules\Credentials\Object\PartyRoleCollection;

class PartyRoleFactory
{
    /**
     * @param \Ocpi\Models\PartyRole $role
     *
     * @return PartyRole
     */
    public static function fromModel(\Ocpi\Models\PartyRole $role): PartyRole
    {
        return new PartyRole(
            $role->id,
            $role->party_id,
            $role->code,
            $role->role,
            $role->country_code,
            (array)$role->business_details ?? [],
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