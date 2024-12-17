<?php

namespace Ocpi\Support\Client\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class PostRequest extends BaseRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct() {}

    protected function defaultBody(): array
    {
        return $this->payload;
    }
}
