<?php

namespace Ocpi\Modules\Commands\Object;

use Illuminate\Contracts\Support\Arrayable;
use Ocpi\Models\Commands\Enums\CommandResponseType;
use Ocpi\Support\Objects\DisplayText;

readonly class CommandResponse implements Arrayable
{
    /**
     * tell the emsp that the response will be there within 60s
     */
    public const int COMMAND_RESPONSE_TIMEOUT = 60;

    /**
     * @param CommandResponseType $result
     * @param DisplayText $message
     * @param int $timeoutSeconds
     */
    public function __construct(
        private CommandResponseType $result,
        private DisplayText $message,
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
            'timeout' => $this->getTimeoutSeconds(),
            'message' => $this->getMessage()->toArray(),
        ];
    }
}