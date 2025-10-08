<?php

namespace Ocpi\Modules\Sessions\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Support\Enums\SessionStatus;

readonly class Session implements Arrayable
{

    /**
     * @param string $id
     * @param int $partyRoleId
     * @param int $locationId
     * @param string $sessionId
     * @param SessionStatus $status
     * @param SessionDetails $sessionDetails
     */
    public function __construct(
        private string $id,
        private int $partyRoleId,
        private int $locationId,
        private string $sessionId,
        private SessionStatus $status,
        private SessionDetails $sessionDetails,
    ) {
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
    public function getPartyRoleId(): int
    {
        return $this->partyRoleId;
    }

    /**
     * @return int
     */
    public function getLocationId(): int
    {
        return $this->locationId;
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @return SessionStatus
     */
    public function getStatus(): SessionStatus
    {
        return $this->status;
    }

    /**
     * @return SessionDetails
     */
    public function getSessionDetails(): SessionDetails
    {
        return $this->sessionDetails;
    }

    /**
     * @return array
     */
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