<?php

namespace Ocpi\Modules\Credentials\Object;

use Illuminate\Contracts\Support\Arrayable;

readonly class PartyToken implements Arrayable
{
    public function __construct(
        private int $id,
        private int $partyRoleId,
        private string $name,
        private string $token,
        private bool $isRegistered,
    ) {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPartyRoleId(): int
    {
        return $this->partyRoleId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return bool
     */
    public function isRegistered(): bool
    {
        return $this->isRegistered;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'party_role_id' => $this->getPartyRoleId(),
            'name' => $this->getName(),
            'token' => $this->getToken(),
            'is_registered' => $this->isRegistered(),
        ];
    }
}