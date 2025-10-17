<?php

namespace Ocpi\Modules\Cdrs\Objects;

use Illuminate\Contracts\Support\Arrayable;

readonly class SignedData implements Arrayable
{
    /**
     * @param string $encodingMethod
     * @param int|null $encodingMethodVersion
     * @param string|null $publicKey
     * @param SignedValueCollection $signedValueCollection
     * @param string $url
     */
    public function __construct(
        private string $encodingMethod,
        private ?int $encodingMethodVersion = null,
        private ?string $publicKey = null,
        private SignedValueCollection $signedValueCollection,
        private string $url
    ) {
    }

    /**
     * @return string
     */
    public function getEncodingMethod(): string
    {
        return $this->encodingMethod;
    }

    /**
     * @return int|null
     */
    public function getEncodingMethodVersion(): ?int
    {
        return $this->encodingMethodVersion;
    }

    /**
     * @return string|null
     */
    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * @return SignedValueCollection
     */
    public function getSignedValueCollection(): SignedValueCollection
    {
        return $this->signedValueCollection;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'encoding_method' => $this->getEncodingMethod(),
            'encoding_method_version' => $this->getEncodingMethodVersion(),
            'public_key' => $this->getPublicKey(),
            'signed_values' => $this->getSignedValueCollection()->toArray(),
            'url' => $this->getUrl(),
        ];
    }
}