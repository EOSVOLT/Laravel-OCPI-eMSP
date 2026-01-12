<?php

namespace Ocpi\Modules\DTOs;

use Illuminate\Contracts\Support\Arrayable;

readonly class RemoteStopTransactionRequestDTO implements Arrayable
{
    public function __construct(
        private string $responseUrl,
        private string $sessionId,
    ) {
    }

    public function getResponseUrl(): string
    {
        return $this->responseUrl;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function toArray(): array
    {
        return [
            'response_url' => $this->getResponseUrl(),
            'session_id' => $this->getSessionId(),
        ];
    }
}