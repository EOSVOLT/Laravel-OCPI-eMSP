<?php

namespace Ocpi\Modules\Sessions\Server\Controllers\CPO\V2_2_1;

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
        string $countryCode,
        string $partyId,
        string $sessionId,
    ): JsonResponse {
        //@todo revisit PUT for setting charging preference.
        return $this->ocpiSuccessResponse();
    }
}
