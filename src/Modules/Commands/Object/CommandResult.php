<?php

namespace Ocpi\Modules\Commands\Object;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Commands\Enums\CommandResultType;
use Ocpi\Support\Objects\DisplayTextCollection;

readonly class CommandResult implements Arrayable
{

    /**
     * @param CommandResultType $result
     * @param DisplayTextCollection $messages
     */
    public function __construct(
        private CommandResultType $result,
        private DisplayTextCollection $messages,
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
     * @return DisplayTextCollection
     */
    public function getMessages(): DisplayTextCollection
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
            'message' => $this->getMessages()->toArray(),
        ];
    }
}