<?php

namespace Ocpi\Modules\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Tokens\Objects\CommandToken;

readonly class RemoteStartTransactionRequestDTO implements Arrayable
{
    public function __construct(
        private string $responseUrl,
        private CommandToken $token,
        private string $locationExternalId,
        private ?string $evseUid = null,
        private ?string $connectorId = null,
        private ?string $authorizationReference = null
    )
    {
    }

    public function getResponseUrl(): string
    {
        return $this->responseUrl;
    }

    public function getToken(): CommandToken
    {
        return $this->token;
    }

    public function getLocationExternalId(): string
    {
        return $this->locationExternalId;
    }

    public function getEvseUid(): ?string
    {
        return $this->evseUid;
    }

    public function getConnectorId(): ?string
    {
        return $this->connectorId;
    }

    public function getAuthorizationReference(): ?string
    {
        return $this->authorizationReference;
    }

    public function toArray(): array
    {
        return [
            'response_url' => $this->responseUrl,
            'token' => $this->getToken()->toArray(),
            'location_id' => $this->getLocationExternalId(),
            'evse_uid' => $this->getEvseUid(),
            'connector_id' => $this->getConnectorId(),
            'authorization_reference' => $this->getAuthorizationReference(),
        ];
    }
}