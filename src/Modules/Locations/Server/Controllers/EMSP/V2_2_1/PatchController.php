<?php

namespace Ocpi\Modules\Locations\Server\Controllers\EMSP\V2_2_1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class PatchController extends Controller
{
    use HandlesLocation;

    public function __invoke(
        Request $request,
        string $countryCode,
        string $partyCode,
        string $locationId,
        ?string $evseUid = null,
        ?string $connectorId = null,
    ): JsonResponse {
        try {
            $payload = $request->all();
            $partyRole = PartyRole::find(Context::get('party_role_id'));
            if ($partyRole->country_code !== $countryCode || $partyRole->code !== $partyCode) {
                return $this->ocpiClientErrorResponse(
                    statusCode: OcpiClientErrorCode::UnknownLocation,
                    statusMessage: 'Unknown Location.',
                );
            }
            // EVSE or Connector.
            if ($evseUid !== null) {
                $locationEvse = $this->evseSearch(
                    $partyRole->party_id,
                    $locationId,
                    $evseUid,
                );

                if (null === $locationEvse || $locationEvse->locationWithTrashed?->id !== $locationId) {
                    return $this->ocpiClientErrorResponse(
                        statusCode: OcpiClientErrorCode::UnknownLocation,
                        statusMessage: 'Unknown Location or EVSE.',
                    );
                }

                // Updated EVSE.
                if (null === $connectorId) {
                    if (
                        !DB::connection(config('ocpi.database.connection'))
                            ->transaction(function () use ($payload, $locationEvse) {
                                return $this->evseObjectUpdate(
                                    payload: $payload,
                                    locationEvse: $locationEvse,
                                );
                            })
                    ) {
                        return $this->ocpiClientErrorResponse(
                            statusCode: OcpiClientErrorCode::NotEnoughInformation,
                        );
                    }
                } // Updated Connector.
                else {
                    $locationConnector = $locationEvse
                        ->connectorsWithTrashed
                        ->where('connector_id', $connectorId)
                        ->first();

                    if ($locationConnector === null) {
                        return $this->ocpiClientErrorResponse(
                            statusCode: OcpiClientErrorCode::UnknownLocation,
                            statusMessage: 'Unknown Connector.',
                        );
                    }

                    if (
                        !DB::connection(config('ocpi.database.connection'))
                            ->transaction(function () use ($payload, $locationConnector, $locationEvse) {
                                return $this->connectorObjectUpdate(
                                    payload: $payload,
                                    locationConnector: $locationConnector,
                                    locationEvse: $locationEvse,
                                );
                            })
                    ) {
                        return $this->ocpiClientErrorResponse(
                            statusCode: OcpiClientErrorCode::NotEnoughInformation,
                            statusMessage: 'Failed to update Connector.',
                        );
                    }
                }
            } // Location.
            else {
                $location = $this->searchLocation(
                    $partyRole,
                    $locationId
                );

                if (null === $location) {
                    return $this->ocpiClientErrorResponse(
                        statusCode: OcpiClientErrorCode::UnknownLocation,
                        statusMessage: 'Unknown Location.',
                    );
                }

                // Updated Location.
                if (
                    !DB::connection(config('ocpi.database.connection'))
                        ->transaction(function () use ($payload, $location) {
                            return $this->locationObjectUpdate(
                                payload: $payload,
                                location: $location,
                            );
                        })
                ) {
                    return $this->ocpiClientErrorResponse(
                        statusCode: OcpiClientErrorCode::NotEnoughInformation,
                        statusMessage: 'Failed to update Location.',
                    );
                }
            }

            return $this->ocpiSuccessResponse();
        } catch (Exception $e) {
            Log::channel('ocpi')->error($e->getMessage());

            return $this->ocpiServerErrorResponse();
        }
    }
}
