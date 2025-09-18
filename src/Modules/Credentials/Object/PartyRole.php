<?php

namespace Ocpi\Modules\Credentials\Object;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Support\Enums\Role;

readonly class PartyRole implements Arrayable
{
    public function __construct(
        private string $id,
        private int $partyId,
        private string $code,
        private Role $role,
        private string $countryCode,
        private array $businessDetails,
    )
    {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPartyId(): int
    {
        return $this->partyId;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return array
     */
    public function getBusinessDetails(): array
    {
        return $this->businessDetails;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'party_id' => $this->getPartyId(),
            'code' => $this->getCode(),
            'role' => $this->getRole()->value,
            'country_code' => $this->getCountryCode(),
            'business_details' => $this->getBusinessDetails(),
        ];
    }
}