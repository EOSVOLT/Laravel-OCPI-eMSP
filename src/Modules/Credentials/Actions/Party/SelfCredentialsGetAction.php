<?php

namespace Ocpi\Modules\Credentials\Actions\Party;

use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;

class SelfCredentialsGetAction
{
    public function handle(Party $party): ?array
    {
        if (null === $party->url) {
            return null;
        }
        if (version_compare($party->version, '2.2', '<')) {
            /** @var PartyRole $role */
            $role = $party->roles->first();
            return [
                'url' => $party->url,
                'token' => $party->encoded_server_token, //token C
                'party_id' => $role->code,
                'country_code' => $role->country_code,
                'business_details' => $role->business_details,
            ];
        } else {
            return [
                'url' => $party->url,
                'token' => $party->encoded_server_token, //token C
                'roles' => $party->roles->map(function (PartyRole $role) {
                    return [
                        'role' => $role->role,
                        'party_id' => $role->code,
                        'country_code' => $role->country_code,
                        'business_details' => $role->business_details,
                    ];
                }),
            ];
        }
    }
}
