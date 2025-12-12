<?php

namespace Ocpi\Modules\Tokens\Server\Responses;

use Ocpi\Modules\Tokens\Enums\AllowedType;
use Ocpi\Modules\Tokens\Objects\CommandToken;
use Ocpi\Modules\Tokens\Objects\LocationReferences;
use Ocpi\Support\Objects\DisplayText;

readonly class AuthorizationInfo
{
    public function __construct(
        private AllowedType $allowedType,
        private CommandToken $token,
        private ?LocationReferences $location = null,
        private ?string $authorizationReference = null,
        private ?DisplayText $info = null,
    )
    {
    }

    public function getAllowedType(): AllowedType
    {
        return $this->allowedType;
    }

    public function getToken(): CommandToken
    {
        return $this->token;
    }

    public function getLocation(): ?LocationReferences
    {
        return $this->location;
    }

    public function getAuthorizationReference(): ?string
    {
        return $this->authorizationReference;
    }

    public function getInfo(): ?DisplayText
    {
        return $this->info;
    }

    public function toArray(): array
    {
        return [
            'allowed_type' => $this->allowedType->value,
            'token' => $this->token->toArray(),
            'location' => $this->location?->toArray(),
            'authorization_reference' => $this->authorizationReference,
            'info' => $this->info?->toArray(),
        ];
    }
}