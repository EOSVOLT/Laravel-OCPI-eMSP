<?php

namespace Ocpi\Support\Objects;

use Illuminate\Contracts\Support\Arrayable;

readonly class DisplayText implements Arrayable
{
    public function __construct(
        private string $language,
        private string $text,
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