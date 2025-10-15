<?php

namespace Ocpi\Modules\Cdrs\Objects;

use Illuminate\Contracts\Support\Arrayable;

readonly class Cdr implements Arrayable
{

    public function __construct(
        private int $id,
        private int $partyRoleId,
        private string $cdrId,
        private CdrDetails $cdrDetails,
        private int $locationId,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPartyRoleId(): int
    {
        return $this->partyRoleId;
    }

    public function getCdrId(): string
    {
        return $this->cdrId;
    }

    public function getCdrDetails(): CdrDetails
    {
        return $this->cdrDetails;
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'party_role_id' => $this->getPartyRoleId(),
            'cdr_id' => $this->getCdrId(),
            'details' => $this->getCdrDetails()->toArray(),
            'location_id' => $this->getLocationId(),
        ];
    }
}