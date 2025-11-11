<?php

namespace Ocpi\Modules\Credentials\Object;

use Illuminate\Contracts\Support\Arrayable;

readonly class Party implements Arrayable
{
    /**
     * @param int $id
     * @param string $code
     * @param string|null $version
     * @param string|null $versionUrl
     * @param PartyRoleCollection|null $roles
     */
    public function __construct(
        private int $id,
        private string $code,
        private ?string $version = null,
        private ?string $versionUrl = null,
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

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function getVersionUrl(): ?string
    {
        return $this->versionUrl;
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
            'version' => $this->getVersion(),
            'version_url' => $this->getVersionUrl(),
        ];
    }
}