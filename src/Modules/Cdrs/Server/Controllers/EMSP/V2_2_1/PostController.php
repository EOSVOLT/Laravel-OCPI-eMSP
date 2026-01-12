<?php

namespace Ocpi\Modules\Cdrs\Server\Controllers\EMSP\V2_2_1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Cdrs\Traits\HandlesCdr;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Enums\Role;
use Ocpi\Support\Server\Controllers\Controller;

class PostController extends Controller
{
    use HandlesCdr,
        HandlesLocation;

    public function __construct()
    {
    }

    public function __invoke(
        Request $request,
    ): JsonResponse {
        try {
            //add a cdr payload validator.

            $payload = $request->json()->all();

            /** @var PartyRole $partyRole */
            $partyRole = PartyRole::query()->where('code', $payload['party_id'])
                ->where('country_code', $payload['country_code'])
                ->where('role', Role::CPO)
                ->first();
            $partyRoleId = $partyRole->id;

            // Verify CDR not already exists.
            $cdr = $this->cdrSearch($payload['id']);

            if (null !== $cdr) {
                return $this->ocpiClientErrorResponse(
                    statusCode: OcpiClientErrorCode::InvalidParameters,
                    statusMessage: 'CDR already exists.',
                );
            }

            // Find LocationEvse.
            $cdrLocation = data_get($payload, 'cdr_location');
            $locationId = $cdrLocation['id'];
            $locationEvseUid = $cdrLocation['evse_uid'];
            $locationEvse = $this->evseSearch(
                partyId: $partyRole->party_id,
                locationExternalId: $locationId,
                evseUid: $locationEvseUid,
            );

            if (null === $locationEvse) {
                return $this->ocpiClientErrorResponse(
                    statusCode: OcpiClientErrorCode::UnknownLocation,
                    statusMessage: 'Location or Evse not found.',
                );
            }

            // New CDR.
            $cdr = DB::connection(config('ocpi.database.connection'))
                ->transaction(function () use ($payload, $partyRoleId, $locationEvse) {
                    return $this->createCdr(
                        payload: $payload,
                        partyRoleId: $partyRoleId,
                        locationEvse: $locationEvse,
                    );
                });

            return (null !== $cdr)
                // Add Location header with CDR GET URL.
                ? $this->ocpiSuccessResponse()
                    ->header('Location', $this->cdrRoute($cdr))
                : $this->ocpiClientErrorResponse(
                    statusCode: OcpiClientErrorCode::NotEnoughInformation,
                );
        } catch (Exception $e) {
            Log::channel('ocpi')->error($e->getMessage());

            return $this->ocpiServerErrorResponse();
        }
    }
}
