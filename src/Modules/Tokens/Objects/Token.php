<?php

namespace Ocpi\Modules\Tokens\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;
use Ocpi\Models\Commands\Enums\ProfileType;
use Ocpi\Models\Commands\Enums\WhitelistType;
use Ocpi\Modules\Locations\Enums\TokenType;

readonly class Token implements Arrayable
{

    /**
     * @param string $countryCode
     * @param string $partyId
     * @param string $tokenUid
     * @param TokenType $type
     * @param string $contractId
     * @param string|null $visual_number
     * @param string $issuer
     * @param string|null $groupId
     * @param bool $valid
     * @param WhitelistType $whitelist
     * @param string|null $language
     * @param ProfileType|null $defaultProfileType
     * @param EnergyContract|null $energyContract
     * @param Carbon $updatedAt
     */
    public function __construct(
        private string $countryCode,
        private string $partyId,
        private string $tokenUid,
        private TokenType $type,
        private string $contractId,
        private ?string $visual_number = null,
        private string $issuer,
        private ?string $groupId = null,
        private bool $valid = false,
        private WhitelistType $whitelist,
        private ?string $language = null,
        private ?ProfileType $defaultProfileType = null,
        private ?EnergyContract $energyContract = null,
        private Carbon $updatedAt,
    ) {
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getPartyId(): string
    {
        return $this->partyId;
    }

    /**
     * @return string
     */
    public function getTokenUid(): string
    {
        return $this->tokenUid;
    }

    /**
     * @return TokenType
     */
    public function getType(): TokenType
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getContractId(): string
    {
        return $this->contractId;
    }

    /**
     * @return string|null
     */
    public function getVisualNumber(): ?string
    {
        return $this->visual_number;
    }

    /**
     * @return string
     */
    public function getIssuer(): string
    {
        return $this->issuer;
    }

    /**
     * @return string|null
     */
    public function getGroupId(): ?string
    {
        return $this->groupId;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @return WhitelistType
     */
    public function getWhitelist(): WhitelistType
    {
        return $this->whitelist;
    }

    /**
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @return ProfileType|null
     */
    public function getDefaultProfileType(): ?ProfileType
    {
        return $this->defaultProfileType;
    }

    /**
     * @return EnergyContract|null
     */
    public function getEnergyContract(): ?EnergyContract
    {
        return $this->energyContract;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    /**
     * @return array
     */
    public function toArray(): array
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
            'last_updated' => $this->getUpdatedAt()->toIsoString(),
        ];
    }
}