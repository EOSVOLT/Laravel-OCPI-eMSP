<?php

namespace Ocpi\Modules\Sessions\Server\Controllers\EMSP\V2_2_1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ocpi\Modules\Sessions\Traits\HandlesSession;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class PatchController extends Controller
{
    use HandlesSession;

    public function __invoke(
        Request $request,
        string $countryCode,
        string $partyId,
        string $sessionId,
    ): JsonResponse {
        try {
            $payload = $request->json()->all();

            $session = $this->sessionById(
                externalSessionId: $sessionId,
                partyRoleId: Context::get('party_role_id'),
            );

            if ($session === null) {
                return $this->ocpiClientErrorResponse(
                    statusCode: OcpiClientErrorCode::InvalidParameters,
                    statusMessage: 'Unknown Session.',
                );
            }

            // Updated Session.
            if (
                !DB::connection(config('ocpi.database.connection'))
                    ->transaction(function () use ($payload, $session) {
                        return $this->sessionObjectUpdate(
                            payload: $payload,
                            session: $session,
                        );
                    })
            ) {
                return $this->ocpiClientErrorResponse(
                    statusCode: OcpiClientErrorCode::NotEnoughInformation,
                );
            }

            return $this->ocpiSuccessResponse();
        } catch (Exception $e) {
            Log::channel('ocpi')->error($e->getMessage());

            return $this->ocpiServerErrorResponse();
        }
    }
}
