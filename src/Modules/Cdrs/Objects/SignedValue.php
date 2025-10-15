<?php

namespace Ocpi\Modules\Cdrs\Objects;

use Illuminate\Contracts\Support\Arrayable;

readonly class SignedValue implements Arrayable
{
    /**
     * @param string $nature
     * @param string $plainData
     * @param string $signedData
     */
    public function __construct(
        private readonly string $nature,
        private readonly string $plainData,
        private readonly string $signedData,
    ) {
    }

    /**
     * @return string
     */
    public function getNature(): string
    {
        return $this->nature;
    }

    /**
     * @return string
     */
    public function getPlainData(): string
    {
        return $this->plainData;
    }

    /**
     * @return string
     */
    public function getSignedData(): string
    {
        return $this->signedData;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'nature' => $this->getNature(),
            'plain_data' => $this->getPlainData(),
            'signed_data' => $this->getSignedData(),
        ];
    }
}