<?php

namespace Ocpi\Modules\Credentials\Console\Commands;

use Illuminate\Support\Str;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Support\Enums\Role;

trait CredentialCommandTrait
{
    public function createPartyRole(Party $party, Role $role, string $countryCode, array $businessDetails): PartyRole
    {
        $partyRole = new PartyRole();
        $partyRole->fill([
            'code' => $party->code,
            'role' => $role,
            'country_code' => $countryCode,
            'business_details' => $businessDetails,
            'url' => config('ocpi.client.server.url') . '/' . strtolower($role->value) . '/versions',
        ]);
        $party->roles()->save($partyRole);

        $token = new PartyToken();
        $token->fill([
            'token' => Str::random(32),
            'registered' => false,
            'name' => 'Token for ' . $role->value . '_' . $party->code . '_' . $countryCode,
        ]);
        $partyRole->tokens()->save($token);
        return $partyRole;
    }
}