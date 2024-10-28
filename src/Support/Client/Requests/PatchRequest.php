<?php

namespace Ocpi\Support\Client\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class PatchRequest extends BaseRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(protected readonly array $payload) {}

    protected function defaultBody(): array
    {
        return $this->payload;
    }
}
