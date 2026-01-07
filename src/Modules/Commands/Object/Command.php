<?php

namespace Ocpi\Modules\Commands\Object;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Commands\Enums\CommandResponseType;
use Ocpi\Modules\Commands\Enums\CommandResultType;
use Ocpi\Modules\Commands\Enums\CommandType;
use Ocpi\Modules\Credentials\Object\PartyRole;

readonly class Command implements Arrayable
{
    public function __construct(
        private string $id,
        private PartyRole $partyRole,
        private CommandType $type,
        private array $payload,
        private ?CommandResponseType $responseType = null,
        private ?CommandResultType $resultType = null,
    ) {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return PartyRole
     */
    public function getPartyRole(): PartyRole
    {
        return $this->partyRole;
    }

    /**
     * @return CommandType
     */
    public function getType(): CommandType
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @return CommandResponseType|null
     */
    public function getResponseType(): ?CommandResponseType
    {
        return $this->responseType;
    }

    /**
     * @return CommandResultType|null
     */
    public function getResultType(): ?CommandResultType
    {
        return $this->resultType;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'party_role_id' => $this->getPartyRole()->getId(),
            'type' => $this->getType()->value,
            'payload' => $this->getPayload(),
            'response_type' => $this->getResponseType()?->value,
            'result_type' => $this->getResultType()?->value,
        ];
    }
}