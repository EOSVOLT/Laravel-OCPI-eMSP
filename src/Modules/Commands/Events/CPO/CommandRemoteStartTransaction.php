<?php

namespace Ocpi\Modules\Commands\Events\CPO;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Ocpi\Models\Commands\Enums\CommandType;

class CommandRemoteStartTransaction implements ShouldDispatchAfterCommit, ReceiverCommandEventInterface
{
    use Dispatchable;

    /**
     * @param string $id
     * @param CommandType $type
     * @param string $locationId
     * @param string|null $evseUid
     * @param string|null $connectorId
     */
    public function __construct(
        private readonly string $id,
        private readonly CommandType $type,
        private readonly string $locationId,
        private readonly ?string $evseUid = null,
        private readonly ?string $connectorId = null,
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
     * @return CommandType
     */
    public function getType(): CommandType
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getLocationId(): string
    {
        return $this->locationId;
    }

    /**
     * @return string|null
     */
    public function getEvseUid(): ?string
    {
        return $this->evseUid;
    }

    /**
     * @return string|null
     */
    public function getConnectorId(): ?string
    {
        return $this->connectorId;
    }

}