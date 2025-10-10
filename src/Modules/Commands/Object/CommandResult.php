<?php

namespace Ocpi\Modules\Commands\Object;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Commands\Enums\CommandResultType;
use Ocpi\Support\Objects\DisplayText;

readonly class CommandResult implements Arrayable
{

    /**
     * @param CommandResultType $result
     * @param DisplayText $message
     */
    public function __construct(
        private CommandResultType $result,
        private DisplayText $message,
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
     * @return DisplayText
     */
    public function getMessage(): DisplayText
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'result' => $this->getResult()->value,
            'message' => $this->getMessage()->toArray(),
        ];
    }
}