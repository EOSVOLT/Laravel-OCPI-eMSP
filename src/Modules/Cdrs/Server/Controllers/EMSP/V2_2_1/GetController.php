<?php

namespace Ocpi\Modules\Cdrs\Server\Controllers\EMSP\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\Party;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Cdrs\Traits\HandlesCdr;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Enums\OcpiServerErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    use HandlesCdr;

    public function __invoke(
        Request $request,
        ?string $cdrId = null,
    ): JsonResponse {
        if ($cdrId === null) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::NotEnoughInformation,
                statusMessage: 'eMSP CDR ID is missing.',
            );
        }
        $cdr = $this->cdrSearch($cdrId);

        if ($cdr === null) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::InvalidParameters,
                statusMessage: 'Unknown CDR.',
            );
        }

        $data = $cdr->object;

        return $data
            ? $this->ocpiSuccessResponse($data)
            : $this->ocpiServerErrorResponse();
    }
}
