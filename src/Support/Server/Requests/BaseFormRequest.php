<?php

namespace Ocpi\Support\Server\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ocpi\Support\Traits\Server\Response as ServerResponse;

class BaseFormRequest extends FormRequest
{
    use ServerResponse;
}