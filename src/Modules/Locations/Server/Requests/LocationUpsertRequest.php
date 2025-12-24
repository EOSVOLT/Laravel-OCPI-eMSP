<?php

namespace Ocpi\Modules\Locations\Server\Requests;

use Ocpi\Modules\Locations\Traits\HandlesLocation;
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



}