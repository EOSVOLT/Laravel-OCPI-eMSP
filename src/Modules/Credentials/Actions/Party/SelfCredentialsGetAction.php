<?php

namespace Ocpi\Modules\Credentials\Actions\Party;

use Ocpi\Models\PartyToken;
use Ocpi\Support\Helpers\GeneratorHelper;

class SelfCredentialsGetAction
{
    public function handle(PartyToken $partyToken): ?array
    {
        if (null === $partyToken->party_role->url) {
            return null;
        }
        $role = $partyToken->party_role;
        $party = $role->party;
        $token = GeneratorHelper::encodeToken($partyToken->token, $party->version);
        if (version_compare($party->version, '2.2', '<')) {
            return [
                'url' => $role->url,
                'token' => $token, //token C
                'party_id' => $role->code,
                'country_code' => $role->country_code,
                'business_details' => $role->business_details,
            ];
        } else {
            return [
                'url' => $role->url,
                'token' => $token, //token C
                'roles' => [
                    [
                        'role' => $role->role,
                        'party_id' => $role->code,
                        'country_code' => $role->country_code,
                        'business_details' => $role->business_details,
                    ]
                ],
            ];
        }
    }
}
