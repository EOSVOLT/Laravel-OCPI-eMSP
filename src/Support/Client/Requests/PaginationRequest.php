<?php

namespace Ocpi\Support\Client\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaginationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'offset' => 'nullable|integer|gte:0',
            'limit' => 'nullable|integer|gt:0',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'offset' => $this->offset ?? 0,
            'limit' => $this->limit ?? 20,
        ]);
    }
}
