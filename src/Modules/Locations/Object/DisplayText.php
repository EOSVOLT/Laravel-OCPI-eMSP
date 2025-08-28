<?php

namespace Ocpi\Modules\Locations\Object;

use Illuminate\Contracts\Support\Arrayable;

class DisplayText implements Arrayable
{
    public function __construct(
        private readonly string $language,
        private readonly string $text,
    )
    {
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "language" => $this->getLanguage(),
            "text" => $this->getText(),
        ];
    }
}