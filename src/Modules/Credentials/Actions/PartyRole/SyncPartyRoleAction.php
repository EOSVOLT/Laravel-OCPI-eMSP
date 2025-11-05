<?php

namespace Ocpi\Modules\Credentials\Actions\PartyRole;

use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Credentials\Events\CredentialsCreated;
use Ocpi\Modules\Credentials\Object\PartyCode;
use Ocpi\Modules\Versions\Actions\PartyInformationAndDetailsSynchronizeAction;

readonly class SyncPartyRoleAction
{

    public function __construct(
        private PartyInformationAndDetailsSynchronizeAction $detailsSynchronizeAction
    ) {
    }

    public function handle(Party $parentParty, array $data): void
    {
        $tokenB = $data['token'];
        $url = $data['url'];
        foreach ($data['roles'] as $role) {
            $partyCode = new PartyCode($role['party_id'], $role['country_code']);

            $childrenParty = $parentParty->children()->where(
                'code',
                $partyCode->getCodeFormatted()
            )->first();
            if ($childrenParty === null) {
                $childrenParty = Party::query()->create(
                    [
                        'code' => $partyCode->getCodeFormatted(),
                        'parent_id' => $parentParty->id,
                        'url' => $url,
                        'version' => $parentParty->version,
                    ]
                );
                $childrenPartyToken = new PartyToken();
                $tokenName = $role['business_details']['name'] ?? '';
                $childrenPartyToken->fill([
                    'token' => $tokenB,
                    'registered' => true,
                    'name' => $tokenName . '_' . $partyCode->getCodeFormatted(),
                ]);
                $childrenParty->tokens()->save($childrenPartyToken);
                // OCPI GET calls for Versions Information and Details of the Party, store OCPI endpoints.
                $childrenParty = $this->detailsSynchronizeAction->handle(
                    $childrenParty,
                    $childrenPartyToken
                );
                CredentialsCreated::dispatch($childrenParty->id);
            }

            $partyRole = new PartyRole;
            $partyRole->fill([
                'code' => $partyCode->getCode(),
                'role' => $role['role'],
                'country_code' => $partyCode->getCountryCode(),
                'business_details' => $role['business_details'],
            ]);
            $childrenParty->roles()->save($partyRole);
        }
    }
}