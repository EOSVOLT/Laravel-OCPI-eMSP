<?php

namespace Ocpi\Support\Client\Requests;


class ListRequest extends PaginationRequest
{
    public function rules(): array
    {
        return [
            'date_from' => ['nullable', 'date_format:Y-m-d\TH:i:s\Z|Y-m-d\TH:i:s.u\Z'],
            'date_to' => ['nullable', 'date_format:Y-m-d\TH:i:s\Z|Y-m-d\TH:i:s.u\Z', 'after:date_from'],
        ];
    }
}