<?php

namespace Ocpi\Modules\Credentials\Actions\PartyRole;

use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Credentials\Events\CredentialsCreated;
use Ocpi\Modules\Credentials\Object\PartyCode;
use Ocpi\Modules\Versions\Actions\PartyInformationAndDetailsSynchronizeAction;
use Throwable;

readonly class SyncPartyRoleAction
{

    public function __construct(
        private PartyInformationAndDetailsSynchronizeAction $detailsSynchronizeAction
    ) {
    }

    /**
     * @throws Throwable
     */
    public function handle(PartyToken $parentToken, array $data): void
    {
        $tokenB = $data['token'];
        $url = $data['url'];
        $parentPartyRole = $parentToken->party_role;
        $parentParty = $parentPartyRole->party;
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
                        'version' => $parentParty->version,
                    ]
                );
            }
            $partyRole = new PartyRole;
            $partyRole->fill([
                'parent_role_id' => $parentPartyRole->id,
                'code' => $partyCode->getCode(),
                'role' => $role['role'],
                'url' => $url,
                'country_code' => $partyCode->getCountryCode(),
                'business_details' => $role['business_details'],
            ]);
            $role = $childrenParty->roles()->save($partyRole);

            $childrenPartyToken = new PartyToken();
            $tokenName = $role['business_details']['name'] ?? '';
            $childrenPartyToken->fill([
                'token' => $tokenB,
                'registered' => true,
                'name' => $tokenName . '_' . $partyCode->getCodeFormatted(),
            ]);
            $role->tokens()->save($childrenPartyToken);
            $childrenParty->refresh();
            // OCPI GET calls for Versions Information and Details of the Party, store OCPI endpoints.
            $this->detailsSynchronizeAction->handle(
                $childrenPartyToken
            );
            CredentialsCreated::dispatch($childrenParty->id);
        }
    }
}