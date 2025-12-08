<?php

namespace Ocpi\Modules\Sessions\Server\Controllers\EMSP\V2_2_1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Modules\Sessions\Traits\HandlesSession;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class PutController extends Controller
{
    use HandlesLocation,
        HandlesSession;

    /**
     * @param Request $request
     * @param string $countryCode
     * @param string $partyId
     * @param string $externalSessionId
     * @return JsonResponse
     * @throws \Throwable
     */
    public function __invoke(
        Request $request,
        string $countryCode,
        string $partyId,
        string $externalSessionId,
    ): JsonResponse {
        try {
            $payload = $request->all();

            $session = $this->sessionById(
                externalSessionId: $externalSessionId,
                partyRoleId: Context::get('party_role_id'),
            );

            // New Session.
            if ($session === null) {
                // Find LocationConnector.
                $connector = null;
                // Regarding the current flow, location_id, evse_uid, connector_id are required for session.
                $externalLocationId = $payload['location_id'];
                $locationEvseUid = $payload['evse_uid'];
                $connectorId = $payload['connector_id'];
                if ($externalLocationId && $locationEvseUid) {
                    $connector = $this->connectorSearch(
                        externalLocationId: $externalLocationId,
                        evseUid: $locationEvseUid,
                        connectorId: $connectorId
                    );
                }

                // New Session.
                if (
                    !DB::connection(config('ocpi.database.connection'))
                        ->transaction(function () use ($payload, $externalSessionId, $connector) {
                            return $this->sessionCreate(
                                payload: $payload,
                                partyRoleId: Context::get('party_role_id'),
                                externalSessionId: $externalSessionId,
                                connector: $connector,
                            );
                        })
                ) {
                    return $this->ocpiClientErrorResponse(
                        statusCode: OcpiClientErrorCode::NotEnoughInformation,
                    );
                }
            } else {
                // Replaced Session.
                if (
                    !DB::connection(config('ocpi.database.connection'))
                        ->transaction(function () use ($payload, $session) {
                            return $this->sessionReplace(
                                payload: $payload,
                                session: $session,
                            );
                        })
                ) {
                    return $this->ocpiClientErrorResponse(
                        statusCode: OcpiClientErrorCode::NotEnoughInformation,
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
