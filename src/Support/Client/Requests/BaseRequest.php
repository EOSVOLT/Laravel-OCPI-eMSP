<?php

namespace Ocpi\Support\Client\Requests;

use Saloon\Http\Request;

class BaseRequest extends Request
{
    public function resolveEndpoint(): string
    {
        return '';
    }
}
