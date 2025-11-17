<?php

namespace Ocpi\Modules\Locations\Server\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ocpi\Modules\Locations\Traits\HandlesLocation;

class LocationUpsertRequest extends FormRequest
{
    use HandlesLocation;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

    }


}