<?php

namespace Ocpi\Modules\Credentials\Validators\V2_2_1;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ocpi\Support\Enums\Role;

class CredentialsValidator
{

    public static function validate(array $input = []): array
    {
        $rules = [
            'token' => 'required',
            'url' => 'required',
            'roles' => 'required|array',
            'roles.role' => ['required', Rule::enum(Role::class)],
            'roles.business_details' => 'required|array:name,website,logo',
            'roles.business_details.name' => 'required',
            'roles.party_id' => 'required',
            'roles.country_code' => 'required',
        ];
        return Validator::make($input, $rules)
            ->validate();
    }
}
