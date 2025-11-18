<?php

namespace Ocpi\Modules\Locations\Server\Controllers\EMSP\V2_2_1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Locations\Server\Requests\LocationUpsertRequest;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class PutController extends Controller
{
    use HandlesLocation;

    public function __invoke(
        LocationUpsertRequest $request,
        string $countryCode,
        string $partyId,
        string $locationId,
        ?string $evseUid = null,
        ?string $connectorId = null,
    ): JsonResponse {
        try {
            $payload = $request->all();
            $partyRole = PartyRole::find(Context::get('party_role_id'));
            if ($partyRole->country_code !== $countryCode || $partyRole->code !== $partyId) {
                return $this->ocpiClientErrorResponse(
                    statusCode: OcpiClientErrorCode::UnknownLocation,
                    statusMessage: 'Unknown Location.',
                );
            }

            $location = $this->searchLocation(
                $partyRole,
                $locationId,
            );

            // EVSE or Connector.
            if (null !== $evseUid) {
                $locationEvse = $this->evseSearch(
                    $partyRole->party_id,
                    $location->id,
                    $evseUid,
                );

                if (null === $locationEvse && null !== $connectorId) {
                    return $this->ocpiClientErrorResponse(
                        statusCode: OcpiClientErrorCode::UnknownLocation,
                        statusMessage: 'Unknown EVSE.',
                    );
                }

                // New EVSE.
                if (null === $locationEvse) {
                    if (
                        !DB::connection(config('ocpi.database.connection'))
                            ->transaction(function () use ($payload, $location, $evseUid) {
                                return $this->evseCreate(
                                    $location,
                                    $evseUid,
                                    $payload,
                                );
                            })
                    ) {
                        return $this->ocpiClientErrorResponse(
                            statusCode: OcpiClientErrorCode::NotEnoughInformation,
                            statusMessage: 'Failed to create EVSE.',
                        );
                    }
                } else {
                    // Replaced EVSE.
                    if (null === $connectorId) {
                        if (
                            !DB::connection(config('ocpi.database.connection'))
                                ->transaction(function () use ($payload, $locationEvse) {
                                    return $this->evseReplace(
                                        $locationEvse,
                                        $payload
                                    );
                                })
                        ) {
                            return $this->ocpiClientErrorResponse(
                                statusCode: OcpiClientErrorCode::NotEnoughInformation,
                                statusMessage: 'Failed to replace EVSE.',
                            );
                        }
                    } // New or replaced Connector.
                    else {
                        if (
                            !DB::connection(config('ocpi.database.connection'))
                                ->transaction(function () use ($payload, $connectorId, $locationEvse) {
                                    return $this->connectorCreateOrReplace(
                                       $locationEvse,
                                        $connectorId,
                                        $payload
                                    );
                                })
                        ) {
                            return $this->ocpiClientErrorResponse(
                                statusCode: OcpiClientErrorCode::NotEnoughInformation,
                            );
                        }
                    }
                }
            } // Location.
            else {
                // New Location.
                if (null === $location) {
                    if (
                        !DB::connection(config('ocpi.database.connection'))
                            ->transaction(function () use ($partyRole, $payload, $locationId) {
                                return $this->locationCreate(
                                    $partyRole,
                                    $locationId,
                                    $payload,
                                );
                            })
                    ) {
                        return $this->ocpiClientErrorResponse(
                            statusCode: OcpiClientErrorCode::NotEnoughInformation,
                            statusMessage: 'Failed to create Location.',
                        );
                    }
                } else {
                    // Replaced Location.
                    if (
                        !DB::connection(config('ocpi.database.connection'))
                            ->transaction(function () use ($payload, $location) {
                                return $this->locationReplace(
                                    $location,
                                    $payload,
                                );
                            })
                    ) {
                        return $this->ocpiClientErrorResponse(
                            statusCode: OcpiClientErrorCode::NotEnoughInformation,
                            statusMessage: 'Failed to replace Location.',
                        );
                    }
                }
            }

            return $this->ocpiSuccessResponse();
        } catch (Exception $e) {
            Log::channel('ocpi')->error($e->getMessage());

            return $this->ocpiServerErrorResponse();
        }
    }
}
