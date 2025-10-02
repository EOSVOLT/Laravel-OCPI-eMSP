<?php

namespace Ocpi\Modules\Cdrs\DTO;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Locations\Enums\TokenType;

readonly class CdrTokenDTO implements Arrayable
{
    /**
     * @param string $countryCode
     * @param string $partyId
     * @param string $uid
     * @param TokenType $type
     * @param string $contractId
     */
    public function __construct(
        private string $countryCode,
        private string $partyId,
        private string $uid,
        private TokenType $type,
        private string $contractId,
    )
    {
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
    public function getUid(): string
    {
        return $this->uid;
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
     * @return array
     */
    public function toArray(): array
    {
        return [
            'country_code' => $this->getCountryCode(),
            'party_id' => $this->getPartyId(),
            'uid' => $this->getUid(),
            'type' => $this->getType()->value,
            'contract_id' => $this->getContractId(),
        ];
    }
}