<?php

namespace Ocpi\Modules\Credentials\Actions\PartyRole;

use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Credentials\Events\CredentialsCreated;
use Ocpi\Modules\Credentials\Events\CredentialsUpdated;
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
            $isPartyCreated = false;
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
                $isPartyCreated = true;
            }
            $childrenPartyRole = PartyRole::query()->updateOrCreate([
                'party_id' => $childrenParty->id,
                'parent_role_id' => $parentPartyRole->id,
                'code' => $partyCode->getCode(),
                'role' => $role['role'],
                'country_code' => $partyCode->getCountryCode(),
            ], [
                'business_details' => $role['business_details'],
                'url' => $url,
            ]);
            //delete all children tokens.
            $childrenPartyRole->tokens()->delete();
            //add new token.
            $childrenPartyToken = new PartyToken();
            $tokenName = $role['business_details']['name'] ?? '';
            $childrenPartyToken->fill([
                'token' => $tokenB,
                'registered' => true,
                'name' => $tokenName . '_' . $partyCode->getCodeFormatted(),
            ]);
            $childrenPartyRole->tokens()->save($childrenPartyToken);
            $childrenParty->refresh();
            // OCPI GET calls for Versions Information and Details of the Party, store OCPI endpoints.
            $this->detailsSynchronizeAction->handle(
                $childrenPartyToken
            );
            if (true === $isPartyCreated) {
                CredentialsCreated::dispatch($childrenParty->id);
            }else{
                CredentialsUpdated::dispatch($childrenParty->id);
            }

        }
    }
}