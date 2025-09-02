<?php

namespace Ocpi\Modules\Locations\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Locations\Enums\TokenType;

class PublishTokenType implements Arrayable
{
    public function __construct(
        private ?string $uid = null,
        private ?TokenType $type = null,
        private ?string $visualNumber = null,
        private ?string $issuer = null,
        private ?string $groupId
    )
    {
    }

    /**
     * @return string|null
     */
    public function getUid(): ?string
    {
        return $this->uid;
    }

    /**
     * @return TokenType|null
     */
    public function getType(): ?TokenType
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getVisualNumber(): ?string
    {
        return $this->visualNumber;
    }

    /**
     * @return string|null
     */
    public function getIssuer(): ?string
    {
        return $this->issuer;
    }

    /**
     * @return string|null
     */
    public function getGroupId(): ?string
    {
        return $this->groupId;
    }

    public function toArray(): array
    {
        return [
            'uid' => $this->uid,
            'type' => $this->type->name,
            'visual_number' => $this->visualNumber,
            'issuer' => $this->issuer,
            'group_id' => $this->groupId,
        ];
    }
}