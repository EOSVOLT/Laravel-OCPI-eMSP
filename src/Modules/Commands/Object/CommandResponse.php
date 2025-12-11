<?php

namespace Ocpi\Modules\Commands\Object;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Modules\Commands\Enums\CommandResponseType;
use Ocpi\Support\Objects\DisplayText;
use Ocpi\Support\Objects\DisplayTextCollection;

readonly class CommandResponse implements Arrayable
{
    /**
     * tell the emsp that the response will be there within 60s
     */
    public const int COMMAND_RESPONSE_TIMEOUT = 60;

    /**
     * @param CommandResponseType $result
     * @param DisplayTextCollection $messages
     * @param int $timeoutSeconds
     */
    public function __construct(
        private CommandResponseType $result,
        private DisplayTextCollection $messages,
        private int $timeoutSeconds = self::COMMAND_RESPONSE_TIMEOUT,
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
    public function getTimeoutSeconds(): int
    {
        return $this->timeoutSeconds;
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
            'timeout' => $this->getTimeoutSeconds(),
            'message' => $this->getMessages()->toArray(),
        ];
    }
}