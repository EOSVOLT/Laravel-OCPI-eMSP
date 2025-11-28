<?php

namespace Ocpi\Modules\Locations\Server\Controllers\EMSP\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Locations\Factories\ConnectorFactory;
use Ocpi\Modules\Locations\Factories\EvseFactory;
use Ocpi\Modules\Locations\Factories\LocationFactory;
use Ocpi\Modules\Locations\Resources\ConnectorResource;
use Ocpi\Modules\Locations\Resources\EvseResource;
use Ocpi\Modules\Locations\Resources\LocationResource;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    use HandlesLocation;

    public function __invoke(
        string $countryCode,
        string $partyId,
        string $locationId,
        ?string $evseUid = null,
        ?string $connectorId = null,
    ): JsonResponse {
        $partyRole = PartyRole::find(Context::get('party_role_id'));
        if ($partyRole->country_code !== $countryCode || $partyRole->code !== $partyId) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::UnknownLocation,
                statusMessage: 'Unknown Location.',
            );
        }
        if (null === $evseUid && null !== $connectorId) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::InvalidParameters,
                statusMessage: 'Invalid parameters.',
            );
        }
        if (null !== $connectorId) {
            $connector = $this->connectorSearch($locationId, $evseUid, $connectorId);
            if (null !== $connector) {
                $connectorObj = ConnectorFactory::fromModel($connector);
                return $this->ocpiSuccessResponse(new ConnectorResource($connectorObj));
            }
        }

        if (null !== $evseUid) {
            $evse = $this->evseSearch($partyId, $locationId, $evseUid);
            if (null !== $evse) {
                $evseObj = EvseFactory::fromModel($evse);
                return $this->ocpiSuccessResponse(new EvseResource($evseObj));
            }
        }

        $location = $this->searchLocation($partyRole, $locationId);
        if (null !== $location) {
            $locationObj = LocationFactory::fromModel($location);
            return $this->ocpiSuccessResponse(new LocationResource($locationObj));
        }

        return $this->ocpiServerErrorResponse();
    }
}
