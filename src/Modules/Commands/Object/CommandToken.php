<?php

namespace Ocpi\Modules\Commands\Object;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Locations\Enums\TokenType;

readonly class CommandToken implements Arrayable
{

    /**
     * @param string $countryCode
     * @param string $partyCode
     * @param string $tokenUid
     * @param TokenType $type
     * @param string $contractId
     * @param string $visual_number
     */
    public function __construct(
        private string $countryCode,
        private string $partyCode,
        private string $tokenUid,
        private TokenType $type,
        private string $contractId,
        private string $visual_number,
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
    public function getPartyCode(): string
    {
        return $this->partyCode;
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
     * @return string
     */
    public function getVisualNumber(): string
    {
        return $this->visual_number;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'country_code' => $this->getCountryCode(),
            'party_code' => $this->getPartyCode(),
            'uid' => $this->getTokenUid(),
            'type' => $this->getType()->value,
            'contract_id' => $this->getContractId(),
            'visual_number' => $this->getVisualNumber(),
        ];
    }
}