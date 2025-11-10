<?php

namespace Ocpi\Modules\Credentials\Object;

use Illuminate\Contracts\Support\Arrayable;

readonly class Party implements Arrayable
{
    /**
     * @param int $id
     * @param string $code
     * @param string|null $token
     * @param string|null $url
     * @param string|null $version
     * @param string|null $versionUrl
     * @param array|null $endpoints
     * @param bool $isRegistered
     * @param PartyRoleCollection|null $roles
     */
    public function __construct(
        private int $id,
        private string $code,
        private ?string $token = null,
        private ?string $version = null,
        private ?string $versionUrl = null,
        private bool $isRegistered = false,
        private ?PartyRoleCollection $roles = null,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function getVersionUrl(): ?string
    {
        return $this->versionUrl;
    }

    public function getEndpoints(): ?array
    {
        return $this->endpoints;
    }

    public function isRegistered(): bool
    {
        return $this->isRegistered;
    }

    public function getRoles(): ?PartyRoleCollection
    {
        return $this->roles;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'code' => $this->getCode(),
            'token' => $this->getToken(),
            'url' => $this->getUrl(),
            'version' => $this->getVersion(),
            'version_url' => $this->getVersionUrl(),
            'endpoints' => $this->getEndpoints(),
            'is_registered' => $this->isRegistered(),
        ];
    }
}