<?php

namespace Ocpi\Modules\Tokens\Server\Requests;

use Illuminate\Validation\Rule;
use Ocpi\Modules\Locations\Enums\TokenType;
use Ocpi\Support\Client\Requests\PaginationRequest;

class GetChargingTokensRequest extends PaginationRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'token_uid' => $this->route('token_uid'),
        ]);
    }

    public function rules(): array
    {
        return [
            'token_uid' => ['required', 'string', 'max:36'],
            'type' => [
                'required',
                Rule::enum(TokenType::class),
            ]
        ];
    }
}