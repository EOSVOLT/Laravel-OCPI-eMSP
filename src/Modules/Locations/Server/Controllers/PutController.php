<?php

namespace Ocpi\Modules\Locations\Server\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class PutController extends Controller
{
    use HandlesLocation;

    public function __invoke(
        Request $request,
        string $country_code,
        string $party_id,
        string $location_id,
        ?string $evse_uid = null,
        ?string $connector_id = null,
    ): JsonResponse {
        try {
            DB::connection(config('ocpi.database.connection'))->beginTransaction();

            $payload = $request->json()->all();

            $location = $this->locationSearch(
                party_role_id: Context::get('party_role_id'),
                location_id: $location_id,
                withTrashed: true,
            );

            // EVSE or Connector.
            if ($evse_uid !== null) {
                $locationEvse = $this->evseSearch(
                    party_role_id: Context::get('party_role_id'),
                    location_id: $location_id,
                    evse_uid: $evse_uid,
                    withTrashed: true,
                );

                if (
                    ($locationEvse !== null && $location?->id !== $location_id)
                    || ($locationEvse === null && $connector_id !== null)
                ) {
                    return $this->ocpiClientErrorResponse(
                        statusCode: OcpiClientErrorCode::UnknownLocation,
                        statusMessage: 'Unknown Location or EVSE.',
                    );
                }

                // New EVSE.
                if ($locationEvse === null) {
                    if (! $this->evseCreate(
                        payload: $payload,
                        location: $location,
                        evse_uid: $evse_uid,
                    )) {
                        DB::connection(config('ocpi.database.connection'))->rollback();

                        return $this->ocpiClientErrorResponse(
                            statusCode: OcpiClientErrorCode::NotEnoughInformation,
                        );
                    }
                } else {
                    // Replaced EVSE.
                    if ($connector_id === null) {
                        if (! $this->evseReplace(
                            payload: $payload,
                            locationEvse: $locationEvse,
                        )) {
                            DB::connection(config('ocpi.database.connection'))->rollback();

                            return $this->ocpiClientErrorResponse(
                                statusCode: OcpiClientErrorCode::NotEnoughInformation,
                            );
                        }
                    } // New or replaced Connector.
                    else {
                        if (! $this->connectorCreateOrReplace(
                            payload: $payload,
                            connector_id: $connector_id,
                            locationEvse: $locationEvse,
                        )) {
                            DB::connection(config('ocpi.database.connection'))->rollback();

                            return $this->ocpiClientErrorResponse(
                                statusCode: OcpiClientErrorCode::NotEnoughInformation,
                            );
                        }
                    }
                }

            } // Location.
            else {
                // New Location.
                if ($location === null) {
                    if (! $this->locationCreate(
                        payload: $payload,
                        party_role_id: Context::get('party_role_id'),
                        location_id: $location_id,
                    )) {
                        DB::connection(config('ocpi.database.connection'))->rollback();

                        return $this->ocpiClientErrorResponse(
                            statusCode: OcpiClientErrorCode::NotEnoughInformation,
                        );
                    }
                } else {
                    // Replaced Location.
                    if (! $this->locationReplace(
                        payload: $payload,
                        location: $location,
                    )) {
                        DB::connection(config('ocpi.database.connection'))->rollback();

                        return $this->ocpiClientErrorResponse(
                            statusCode: OcpiClientErrorCode::NotEnoughInformation,
                        );
                    }
                }
            }

            DB::connection(config('ocpi.database.connection'))->commit();

            return $this->ocpiSuccessResponse();
        } catch (Exception $e) {
            DB::connection(config('ocpi.database.connection'))->rollback();

            Log::channel('ocpi')->error($e->getMessage());

            return $this->ocpiServerErrorResponse();
        }
    }
}
