<?php

namespace Ocpi\Modules\Sessions\Events\EMSP;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class SessionUpdated implements ShouldDispatchAfterCommit, ReceiverSessionEventInterface
{
    use Dispatchable;

    /**
     * @param int $partyRoleId
     * @param string $id
     * @param array $updateData
     */
    public function __construct(
        private readonly int $partyRoleId,
        private readonly string $id,
        private readonly array $updateData,
    ) {
    }

    /**
     * @return int
     */
    public function getPartyRoleId(): int
    {
        return $this->partyRoleId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getUpdateData(): array
    {
        return $this->updateData;
    }
}
