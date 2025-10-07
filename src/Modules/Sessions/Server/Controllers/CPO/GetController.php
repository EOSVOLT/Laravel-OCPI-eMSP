<?php

namespace Ocpi\Modules\Sessions\Server\Controllers\CPO;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Context;
use Ocpi\Modules\Sessions\Traits\HandlesSession;
use Ocpi\Support\Client\Requests\ListRequest;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    use HandlesSession;

    public function __invoke(ListRequest $request): JsonResponse {
        $dateFrom = Carbon::createFromTimeString($request->input('date_from'));
        $dateTo = Carbon::createFromTimeString($request->input('date_to'));
        $sessions = $this->sessionSearch(
            $dateFrom,
            $dateTo,
            $request->input('offset'),
            $request->input('limit')
        );

        $data = $sessions->pluck('session_details');

        return $this->ocpiSuccessResponse($data);
    }
}
