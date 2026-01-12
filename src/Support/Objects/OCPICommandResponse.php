<?php

namespace Ocpi\Support\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Commands\Enums\CommandResponseType;

readonly class OCPICommandResponse implements Arrayable
{
    public function __construct(
        private CommandResponseType $result,
        private int $timeout,
        private ?DisplayTextCollection $messages = null,
    ) {
    }

    /**
     * @return CommandResponseType
     */
    public function getResult(): CommandResponseType
    {
        return $this->result;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @return DisplayTextCollection|null
     */
    public function getMessages(): ?DisplayTextCollection
    {
        return $this->messages;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'result' => $this->getResult()->value,
            'timeout' => $this->getTimeout(),
            'messages' => $this->getMessages()?->toArray() ?? [],
        ];
    }
}