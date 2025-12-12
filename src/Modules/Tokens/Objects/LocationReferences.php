<?php

namespace Ocpi\Modules\Tokens\Objects;

class LocationReferences
{
    public function __construct(
        private string $locationId,
        private array $evseIds = [],
    )
    {
    }

    public function getLocationId(): string
    {
        return $this->locationId;
    }

    public function getEvseIds(): array
    {
        return $this->evseIds;
    }

    public function toArray(): array
    {
        return [
            'location_id' => $this->getLocationId(),
            'evse_ids' => $this->getEvseIds(),
        ];
    }
}