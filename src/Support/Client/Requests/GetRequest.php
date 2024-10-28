<?php

namespace Ocpi\Support\Client\Requests;

use Saloon\Enums\Method;

class GetRequest extends BaseRequest
{
    protected Method $method = Method::GET;
}
