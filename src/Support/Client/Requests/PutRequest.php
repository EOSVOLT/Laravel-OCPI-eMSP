<?php

namespace Ocpi\Support\Client\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class PutRequest extends BaseRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(protected readonly array $payload) {}

    protected function defaultBody(): array
    {
        return $this->payload;
    }
}
