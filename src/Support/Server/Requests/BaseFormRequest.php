<?php

namespace Ocpi\Support\Server\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Traits\Server\Response as ServerResponse;

class BaseFormRequest extends FormRequest
{
    use ServerResponse;

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::InvalidParameters,
                statusMessage: $validator->getMessageBag()->first(),
            )
        );
    }
}