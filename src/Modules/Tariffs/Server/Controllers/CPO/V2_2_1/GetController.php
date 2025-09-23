<?php

namespace Ocpi\Modules\Tariffs\Server\Controllers\CPO\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Server\Controllers\Controller;
use Ocpi\Support\Traits\PageConvertor;

class GetController extends Controller
{
    use HandlesLocation;
    use PageConvertor;
    /**
     * GET /tariffs
     */
    public function list(Request $request): JsonResponse
    {
        // Minimal implementation: return empty list with OCPI pagination headers
        $offset = (int) $request->input('offset', 0);
        $limit = (int) $request->input('limit', 20);
        $page = self::fromOffset($offset, $limit);

        $data = [];
        $total = 0;

        return $this->ocpiSuccessPaginateResponse(
            $data,
            $page,
            $limit,
            $total,
            self::getLocationPath(Context::get('ocpi_version')),
        );
    }
}
