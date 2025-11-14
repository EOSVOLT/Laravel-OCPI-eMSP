<?php

namespace Ocpi\Support\Objects;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Support\Arrayable;

readonly class OCPIResponse implements Arrayable
{
    public function __construct(
        private int $statusCode,
        private CarbonInterface $timestamp,
        private array|string|object|null $data = null,
        private ?string $statusMessage,
    ) {}

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getTimestamp(): CarbonInterface
    {
        return $this->timestamp;
    }

    public function getData(): object|array|string|null
    {
        return $this->data;
    }

    public function getStatusMessage(): ?string
    {
        return $this->statusMessage;
    }

    public function toArray(): array
    {
        return [
            'status_code' => $this->getStatusCode(),
            'timestamp' => $this->getTimestamp()->toISOString(),
            'data' => $this->getData(),
            'status_message' => $this->getStatusMessage(),
        ];
    }
}
