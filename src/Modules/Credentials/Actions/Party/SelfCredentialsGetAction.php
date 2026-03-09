<?php

namespace Ocpi\Modules\Credentials\Actions\Party;

use Ocpi\Models\PartyToken;

class SelfCredentialsGetAction
{
    public function handle(PartyToken $partyToken): ?array
    {
        if (null === $partyToken->party_role->url) {
            return null;
        }
        $role = $partyToken->party_role;
        $party = $role->party;
        if (version_compare($party->version, '2.2', '<')) {
            return [
                'url' => $role->url,
                'token' => $partyToken->token, //token C
                'party_id' => $role->code,
                'country_code' => $role->country_code,
                'business_details' => $role->business_details,
            ];
        } else {
            $roles = [
                [
                    'role' => $role->role,
                    'party_id' => $role->code,
                    'country_code' => $role->country_code,
                    'business_details' => $role->business_details,
                ],
            ];
            foreach ($role->join_party_roles as $joinRole) {
                $roles[] = [
                    'role' => $joinRole->role,
                    'party_id' => $joinRole->code,
                    'country_code' => $joinRole->country_code,
                    'business_details' => $joinRole->business_details,
                ];
            }
            return [
                'url' => $role->url,
                'token' => $partyToken->token, //token C
                'roles' => $roles,
            ];
        }
    }
}
