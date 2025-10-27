<?php

namespace Ocpi\Modules\Sessions\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Locations\Objects\Connector;
use Ocpi\Modules\Locations\Objects\Location;
use Ocpi\Support\Enums\SessionStatus;

readonly class Session implements Arrayable
{

    /**
     * @param string $id
     * @param int $partyRoleId
     * @param int $locationId
     * @param int $locationEvseId
     * @param int $locationConnectorId
     * @param string $sessionId
     * @param SessionStatus $status
     * @param SessionDetails $sessionDetails
     * @param Connector|null $locationConnector
     */
    public function __construct(
        private string $id,
        private int $partyRoleId,
        private int $locationId,
        private int $locationEvseId,
        private int $locationConnectorId,
        private string $sessionId,
        private SessionStatus $status,
        private SessionDetails $sessionDetails,
        private readonly ?Connector $locationConnector = null,
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
     * @return int
     */
    public function getLocationEvseId(): int
    {
        return $this->locationEvseId;
    }

    /**
     * @return int
     */
    public function getLocationConnectorId(): int
    {
        return $this->locationConnectorId;
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
     * @return Connector|null
     */
    public function getLocationConnector(): ?Connector
    {
        return $this->locationConnector;
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
            'location_evse_id' => $this->getLocationEvseId(),
            'location_connector_id' => $this->getLocationConnectorId(),
            'session_id' => $this->getSessionId(),
            'status' => $this->getStatus()->value,
            'details' => $this->getSessionDetails()->toArray(),
        ];
    }
}