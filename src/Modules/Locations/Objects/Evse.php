<?php

namespace Ocpi\Modules\Locations\Objects;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Locations\Enums\EvseCapability;
use Ocpi\Modules\Locations\Enums\EvseStatus;
use Ocpi\Modules\Locations\Enums\ParkingRestriction;
use Ocpi\Trait\ValidateArrayEnum;

class Evse implements Arrayable
{
    use ValidateArrayEnum;
    /**
     * @var string|null
     */
    private ?string $evseId = null;
    /**
     * @var StatusScheduleCollection|null
     */
    private ?StatusScheduleCollection $statusScheduleCollection = null;
    /**
     * @var EvseCapability|null
     */
    private ?EvseCapability $capabilities = null;
    /**
     * @var string|null
     */
    private ?string $floorLevel = null;
    /**
     * @var GeoLocation|null
     */
    private ?GeoLocation $coordinates = null;
    /**
     * @var string|null
     */
    private ?string $physicalReference = null;
    /**
     * @var DisplayTextCollection|null
     */
    private ?DisplayTextCollection $directions = null;
    /**
     * @var array
     */
    private array $parkingRestrictions = [];
    /**
     * @var ImageCollection|null
     */
    private ?ImageCollection $images = null;

    /**
     * @param string $uid
     * @param EvseStatus $status
     * @param ConnectorCollection $connectors
     * @param Carbon $lastUpdated
     */
    public function __construct(
        private string $uid,
        private EvseStatus $status,
        private ConnectorCollection $connectors,
        private Carbon $lastUpdated,
    ) {
    }

    /**
     * @return string|null
     */
    public function getEvseId(): ?string
    {
        return $this->evseId;
    }

    /**
     * @param string|null $evseId
     *
     * @return $this
     */
    public function setEvseId(?string $evseId): self
    {
        $this->evseId = $evseId;
        return $this;
    }

    /**
     * @return StatusScheduleCollection|null
     */
    public function getStatusScheduleCollection(): ?StatusScheduleCollection
    {
        return $this->statusScheduleCollection;
    }

    /**
     * @param StatusScheduleCollection|null $statusScheduleCollection
     *
     * @return $this
     */
    public function setStatusScheduleCollection(?StatusScheduleCollection $statusScheduleCollection): self
    {
        $this->statusScheduleCollection = $statusScheduleCollection;
        return $this;
    }

    /**
     * @return EvseCapability|null
     */
    public function getCapabilities(): ?EvseCapability
    {
        return $this->capabilities;
    }

    /**
     * @param EvseCapability|null $capabilities
     *
     * @return $this
     */
    public function setCapabilities(?EvseCapability $capabilities): self
    {
        $this->capabilities = $capabilities;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFloorLevel(): ?string
    {
        return $this->floorLevel;
    }

    /**
     * @param string|null $floorLevel
     *
     * @return $this
     */
    public function setFloorLevel(?string $floorLevel): self
    {
        $this->floorLevel = $floorLevel;
        return $this;
    }

    /**
     * @return GeoLocation|null
     */
    public function getCoordinates(): ?GeoLocation
    {
        return $this->coordinates;
    }

    /**
     * @param GeoLocation|null $coordinates
     *
     * @return $this
     */
    public function setCoordinates(?GeoLocation $coordinates): self
    {
        $this->coordinates = $coordinates;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhysicalReference(): ?string
    {
        return $this->physicalReference;
    }

    /**
     * @param string|null $physicalReference
     *
     * @return $this
     */
    public function setPhysicalReference(?string $physicalReference): self
    {
        $this->physicalReference = $physicalReference;
        return $this;
    }

    /**
     * @return DisplayTextCollection|null
     */
    public function getDirections(): ?DisplayTextCollection
    {
        return $this->directions;
    }

    /**
     * @param DisplayTextCollection|null $directions
     *
     * @return $this
     */
    public function setDirections(?DisplayTextCollection $directions): self
    {
        $this->directions = $directions;
        return $this;
    }

    /**
     * @return ImageCollection|null
     */
    public function getImages(): ?ImageCollection
    {
        return $this->images;
    }

    /**
     * @param ImageCollection|null $images
     *
     * @return $this
     */
    public function setImages(?ImageCollection $images): self
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     *
     * @return $this
     */
    public function setUid(string $uid): self
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @return EvseStatus
     */
    public function getStatus(): EvseStatus
    {
        return $this->status;
    }

    /**
     * @param EvseStatus $status
     *
     * @return $this
     */
    public function setStatus(EvseStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return ConnectorCollection
     */
    public function getConnectors(): ConnectorCollection
    {
        return $this->connectors;
    }

    /**
     * @param ConnectorCollection $connectors
     *
     * @return $this
     */
    public function setConnectors(ConnectorCollection $connectors): self
    {
        $this->connectors = $connectors;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getLastUpdated(): Carbon
    {
        return $this->lastUpdated;
    }

    /**
     * @param Carbon $lastUpdated
     *
     * @return $this
     */
    public function setLastUpdated(Carbon $lastUpdated): self
    {
        $this->lastUpdated = $lastUpdated;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getParkingRestrictions(): ?array
    {
        return $this->parkingRestrictions;
    }

    /**
     * @param array $parkingRestrictions
     *
     * @return $this
     */
    public function setParkingRestrictions(array $parkingRestrictions): self
    {
        self::validateArrayEnum($parkingRestrictions, ParkingRestriction::cases());
        $this->parkingRestrictions = $parkingRestrictions;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'uid' => $this->getUid(),
            'evse_id' => $this->getEvseId(),
            'status' => $this->getStatus()->value,
            'status_schedule' => $this->getStatusScheduleCollection()?->toArray(),
            'capabilities' => $this->getCapabilities()?->value,
            'connectors' => $this->getConnectors()->toArray(),
            'floor_level' => $this->getFloorLevel(),
            'coordinates' => $this->getCoordinates()?->toArray(),
            'physical_reference' => $this->getPhysicalReference(),
            'directions' => $this->getDirections()?->toArray(),
            'parking_restrictions' => $this->getParkingRestrictions(),
            'images' => $this->getImages()?->toArray(),
            'last_updated' => $this->getLastUpdated()->toISOString(),
        ];
    }
}