<?php

namespace Ocpi\Modules\Credentials\Object;

use Illuminate\Contracts\Support\Arrayable;

readonly class Party implements Arrayable
{
    /**
     * @param int $id
     * @param string $name
     * @param string $code
     * @param string|null $serverToken
     * @param string|null $url
     * @param string|null $version
     * @param string|null $versionUrl
     * @param array|null $endpoints
     * @param string|null $clientToken
     * @param bool $isRegistered
     * @param string|null $cpoId
     * @param PartyRoleCollection|null $roles
     */
    public function __construct(
        private int $id,
        private string $name,
        private string $code,
        private ?string $serverToken = null,
        private ?string $url = null,
        private ?string $version = null,
        private ?string $versionUrl = null,
        private ?array $endpoints = null,
        private ?string $clientToken = null,
        private bool $isRegistered = false,
        private ?string $cpoId = null,
        private ?PartyRoleCollection $roles = null,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getServerToken(): ?string
    {
        return $this->serverToken;
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

    public function getClientToken(): ?string
    {
        return $this->clientToken;
    }

    public function isRegistered(): bool
    {
        return $this->isRegistered;
    }

    public function getCpoId(): ?string
    {
        return $this->cpoId;
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
            'id' => $this->id,
            'name' => $this->getName(),
            'code' => $this->getCode(),
            'server_token' => $this->getServerToken(),
            'url' => $this->getUrl(),
            'version' => $this->getVersion(),
            'version_url' => $this->getVersionUrl(),
            'endpoints' => $this->getEndpoints(),
            'client_token' => $this->getClientToken(),
            'is_registered' => $this->isRegistered(),
            'cpo_id' => $this->getCpoId(),
        ];
    }
}