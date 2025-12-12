<?php

namespace Ocpi\Modules\Tokens\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Ocpi\Modules\Tokens\Objects\CommandToken;

/**
 * @mixin CommandToken
 */
class CommandTokenResource extends JsonResource
{
    public function __construct(CommandToken $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'country_code' => $this->getCountryCode(),
            'party_id' => $this->getPartyId(),
            'uid' => $this->getTokenUid(),
            'type' => $this->getType()->value,
            'contract_id' => $this->getContractId(),
            'visual_number' => $this->getVisualNumber(),
            'issuer' => $this->getIssuer(),
            'group_id' => $this->getGroupId(),
            'valid' => $this->isValid(),
            'whitelist' => $this->getWhitelist()->value,
            'language' => $this->getLanguage(),
            'default_profile_type' => $this->getDefaultProfileType()?->value,
            'energy_contract' => $this->getEnergyContract()?->toArray(),
            'last_updated' => $this->getUpdatedAt()->toISOString(),
        ];
    }
}
