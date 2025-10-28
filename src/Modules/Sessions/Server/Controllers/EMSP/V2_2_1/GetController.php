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
        ?string $country_code = null,
        ?string $party_id = null,
        ?string $session_id = null,
    ): JsonResponse {
        if ($session_id === null) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::NotEnoughInformation,
                statusMessage: 'Session ID is missing.',
            );
        }

        $session = $this->sessionById(
            session_id: $session_id,
            party_role_id: Context::get('party_role_id'),
        );

        if ($session === null) {
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
