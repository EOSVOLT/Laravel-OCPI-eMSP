<?php

namespace Ocpi\Modules\Locations\Objects;

use Illuminate\Contracts\Support\Arrayable;

class BusinessDetails implements Arrayable
{
    /**
     * @var string|null
     */
    private ?string $website = null;
    /**
     * @var Image|null
     */
    private ?Image $logo = null;

    /**
     * @param string $name
     */
    public function __construct(
        private readonly string $name,
    ) {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string|null $website
     *
     * @return $this
     */
    public function setWebsite(?string $website): self
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @return Image|null
     */
    public function getLogo(): ?Image
    {
        return $this->logo;
    }

    /**
     * @param Image|null $logo
     *
     * @return $this
     */
    public function setLogo(?Image $logo): self
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'website' => $this->getWebsite(),
            'logo' => $this->getLogo(),
        ];
    }
}