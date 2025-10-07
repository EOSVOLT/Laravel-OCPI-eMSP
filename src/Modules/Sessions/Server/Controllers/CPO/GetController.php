<?php

namespace Ocpi\Modules\Sessions\Server\Controllers\CPO;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Context;
use Ocpi\Modules\Sessions\Traits\HandlesSession;
use Ocpi\Support\Client\Requests\ListRequest;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    use HandlesSession;

    public function __invoke(ListRequest $request): JsonResponse {

        $session = $this->sessionSearch(
            $request->input('date_from'),
            $request->input('date_to'),
            $request->input('offset'),
            $request->input('limit')
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
