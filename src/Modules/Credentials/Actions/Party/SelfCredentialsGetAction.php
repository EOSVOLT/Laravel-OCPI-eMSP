<?php

namespace Ocpi\Modules\Credentials\Actions\Party;

use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Support\Helpers\GeneratorHelper;

class SelfCredentialsGetAction
{
    public function handle(PartyRole $partyRole, PartyToken $partyToken): ?array
    {
        if (null === $partyRole->url) {
            return null;
        }
        $token = GeneratorHelper::encodeToken($partyToken->token, $partyRole->party->version);
        if (version_compare($partyRole->party->version, '2.2', '<')) {
            $role = $partyRole;

            return [
                'url' => $partyRole->url,
                'token' => $token, //token C
                'party_id' => $role->code,
                'country_code' => $role->country_code,
                'business_details' => $role->business_details,
            ];
        } else {
            return [
                'url' => $partyRole->url,
                'token' => $token, //token C
                'roles' => [
                    'role' => $partyRole->role,
                    'party_id' => $partyRole->code,
                    'country_code' => $partyRole->country_code,
                    'business_details' => $partyRole->business_details,
                ],
            ];
        }
    }
}
