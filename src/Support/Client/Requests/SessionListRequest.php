<?php

namespace Ocpi\Support\Client\Requests;

class SessionListRequest extends PaginationRequest
{
    public function rules(): array
    {
        return [
            'date_from' => ['required', 'regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,6})?(?:Z)?$/'],
            'date_to' => [
                'nullable',
                'regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,6})?(?:Z)?$/',
                'after:date_from',
            ],
        ];
    }
}
