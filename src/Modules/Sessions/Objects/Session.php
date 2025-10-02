<?php

namespace Ocpi\Modules\Sessions\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Support\Enums\SessionStatus;

class Session implements Arrayable
{

    public function __construct(
        private readonly string $id,
        private readonly int $partyRoleId,
        private readonly int $locationId,
        private readonly string $sessionId,
        private readonly SessionStatus $status,
        private readonly SessionDetails $sessionDetails,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPartyRoleId(): int
    {
        return $this->partyRoleId;
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getStatus(): SessionStatus
    {
        return $this->status;
    }

    public function getSessionDetails(): SessionDetails
    {
        return $this->sessionDetails;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'party_role_id' => $this->getPartyRoleId(),
            'location_id' => $this->getLocationId(),
            'session_id' => $this->getSessionId(),
            'status' => $this->getStatus()->value,
            'details' => $this->getSessionDetails()->toArray(),
        ];
    }
}