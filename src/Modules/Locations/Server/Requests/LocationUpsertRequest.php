<?php

namespace Ocpi\Modules\Locations\Server\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Requests\BaseFormRequest;

class LocationUpsertRequest extends BaseFormRequest
{
    use HandlesLocation;

    public function rules(): array
    {
        $evseUid = $this->route('evseUid');
        $connectorId = $this->route('connector_id');

        if (null !== $connectorId) {
            return $this->connectorRules(false);
        }
        if (null !== $evseUid) {
            return $this->evseRules(false) + $this->connectorRules();
        }

        return $this->locationRules() + $this->evseRules() + $this->connectorRules();
    }

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