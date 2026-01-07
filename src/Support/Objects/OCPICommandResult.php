<?php

namespace Ocpi\Support\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Commands\Enums\CommandResultType;

class OCPICommandResult implements Arrayable
{
    public function __construct(
        private CommandResultType $result,
        private ?DisplayTextCollection $messages = null,
    ) {
    }

    /**
     * @return CommandResultType
     */
    public function getResult(): CommandResultType
    {
        return $this->result;
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
            'messages' => $this->getMessages()?->toArray(),
        ];
    }
}