<?php

namespace Ocpi\Modules\Locations\Object;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Locations\Enums\ImageCategory;

class Image implements Arrayable
{
    /**
     * @var string|null
     */
    private ?string $thumbnail = null;
    /**
     * @var int|null
     */
    private ?int $width = null;
    /**
     * @var int|null
     */
    private ?int $height = null;

    /**
     * @param string $url
     * @param ImageCategory $category
     * @param string $type
     */
    public function __construct(
        private readonly string $url,
        private readonly ImageCategory $category,
        private readonly string $type,

    ) {
    }

    /**
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * @param string|null $thumbnail
     *
     * @return $this
     */
    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     *
     * @return $this
     */
    public function setWidth(?int $width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     *
     * @return $this
     */
    public function setHeight(?int $height): self
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return ImageCategory
     */
    public function getCategory(): ImageCategory
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'url' => $this->getUrl(),
            'thumbnail' => $this->getThumbnail(),
            'category' => $this->getCategory()->value,
            'type' => $this->getType(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
        ];
    }
}