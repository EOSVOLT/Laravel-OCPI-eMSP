<?php

namespace Ocpi\Support\Client\Requests;

use Saloon\Enums\Method;

class DeleteRequest extends BaseRequest
{
    protected Method $method = Method::DELETE;
}
