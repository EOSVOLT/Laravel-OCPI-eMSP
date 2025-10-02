<?php

namespace Ocpi\Modules\Sessions\Server\Controllers\CPO;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Modules\Sessions\Traits\HandlesSession;
use Ocpi\Support\Server\Controllers\Controller;

class PutController extends Controller
{
    use HandlesLocation,
        HandlesSession;

    public function __invoke(
        Request $request,
        string $country_code,
        string $party_id,
        string $session_id,
    ): JsonResponse {
        //PUT for setting charging preference.
        return $this->ocpiSuccessResponse();
    }
}
