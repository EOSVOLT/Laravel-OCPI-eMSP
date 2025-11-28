<?php

namespace Ocpi\Modules\Sessions\Server\Controllers\EMSP\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Modules\Sessions\Traits\HandlesSession;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    use HandlesSession;

    public function __invoke(
        Request $request,
        string $countryCode,
        string $partyId,
        string $sessionId,
    ): JsonResponse {

        $session = $this->sessionById(
            externalSessionId: $sessionId,
            partyRoleId: Context::get('party_role_id'),
        );

        if (null === $session) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::InvalidParameters,
                statusMessage: 'Unknown Session.',
            );
        }

        $data = $session->object;

        return $data
            ? $this->ocpiSuccessResponse($data)
            : $this->ocpiServerErrorResponse();
    }
}
