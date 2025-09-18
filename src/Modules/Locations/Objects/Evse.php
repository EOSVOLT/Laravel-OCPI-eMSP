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
    protected ?string $evseId = null;
    /**
     * @var StatusScheduleCollection|null
     */
    protected ?StatusScheduleCollection $statusScheduleCollection = null;
    /**
     * @var EvseCapability|null
     */
    protected ?EvseCapability $capabilities = null;
    /**
     * @var string|null
     */
    protected ?string $floorLevel = null;
    /**
     * @var GeoLocation|null
     */
    protected ?GeoLocation $coordinates = null;
    /**
     * @var string|null
     */
    protected ?string $physicalReference = null;
    /**
     * @var DisplayTextCollection|null
     */
    protected ?DisplayTextCollection $directions = null;
    /**
     * @var array
     */
    protected array $parkingRestrictions = [];
    /**
     * @var ImageCollection|null
     */
    protected ?ImageCollection $images = null;

    /**
     * @param int $locationId
     * @param string $uid
     * @param EvseStatus $status
     * @param ConnectorCollection $connectors
     * @param Carbon $lastUpdated
     * @param string|null $id
     */
    public function __construct(
        private readonly int $locationId,
        private readonly string $uid,
        private readonly EvseStatus $status,
        private readonly ConnectorCollection $connectors,
        private readonly Carbon $lastUpdated,
        private readonly ?string $id = null,
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
     * @return EvseStatus
     */
    public function getStatus(): EvseStatus
    {
        return $this->status;
    }


    /**
     * @return ConnectorCollection
     */
    public function getConnectors(): ConnectorCollection
    {
        return $this->connectors;
    }


    /**
     * @return Carbon
     */
    public function getLastUpdated(): Carbon
    {
        return $this->lastUpdated;
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

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'location_id' => $this->getLocationId(),
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