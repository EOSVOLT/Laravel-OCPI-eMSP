<?php

namespace Ocpi\Modules\Cdrs\Objects;

use Illuminate\Contracts\Support\Arrayable;

readonly class Cdr implements Arrayable
{

    /**
     * @param int $id
     * @param int $partyRoleId
     * @param string $cdrId
     * @param CdrDetails $cdrDetails
     * @param int $locationId
     * @param int $locationEvseId
     * @param string|null $sessionId
     * @param string|null $externalUrl
     */
    public function __construct(
        private int $id,
        private int $partyRoleId,
        private string $cdrId,
        private CdrDetails $cdrDetails,
        private int $locationId,
        private int $locationEvseId,
        private ?string $sessionId = null,
        private ?string $externalUrl = null,
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
    public function getCdrId(): string
    {
        return $this->cdrId;
    }

    /**
     * @return CdrDetails
     */
    public function getCdrDetails(): CdrDetails
    {
        return $this->cdrDetails;
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
     * @return string|null
     */
    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    /**
     * @return string|null
     */
    public function getExternalUrl(): ?string
    {
        return $this->externalUrl;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'party_role_id' => $this->getPartyRoleId(),
            'cdr_id' => $this->getCdrId(),
            'details' => $this->getCdrDetails()->toArray(),
            'location_id' => $this->getLocationId(),
            'location_evse_id' => $this->getLocationEvseId(),
            'session_id' => $this->getSessionId(),
            'external_url' => $this->getExternalUrl(),
        ];
    }
}