<?php

namespace Ocpi\Modules\Locations\Server\Controllers\CPO;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\Locations\Location;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Client\Client;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    use HandlesLocation;

    public function __invoke(
        Request $request,
        ?string $date_form = null,
        ?string $date_to = null,
        ?string $offset = null,
        ?string $limit = null,
    ): JsonResponse {
        $party = $request->get('party');
            $location = Location::query();
        return []
            ? $this->ocpiSuccessResponse([])
            : $this->ocpiServerErrorResponse();
    }
}
