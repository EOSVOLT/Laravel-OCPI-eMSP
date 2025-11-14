<?php

namespace Ocpi\Support\Client\Requests;

class ListRequest extends PaginationRequest
{
    public function rules(): array
    {
        return [
            'date_from' => ['nullable', 'date_format:"Y-m-d\TH:i:s\Z","Y-m-d\TH:i:s.u\Z","Y-m-d\TH:i:s"'],
            'date_to' => [
                'nullable',
                'date_format:"Y-m-d\TH:i:s\Z","Y-m-d\TH:i:s.u\Z","Y-m-d\TH:i:s"',
                'after:date_from',
            ],
        ];
    }
}
