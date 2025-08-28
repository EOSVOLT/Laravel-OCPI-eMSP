<?php

namespace Ocpi\Modules\Locations\Object;

use Illuminate\Contracts\Support\Arrayable;

class AdditionalGeoLocation implements Arrayable
{
    /**
     * @var string|null
     */
    private ?string $name = null;
    public function __construct(
        private readonly GeoLocation $geoLocation,
    )
    {
    }

    /**
     * @return GeoLocation
     */
    public function getGeoLocation(): GeoLocation
    {
        return $this->geoLocation;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'geo_location' => $this->geoLocation->toArray(),
            'name' => $this->name,
        ];
    }
}