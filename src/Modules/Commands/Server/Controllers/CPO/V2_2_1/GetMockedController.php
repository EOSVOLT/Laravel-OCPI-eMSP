<?php

namespace Ocpi\Modules\Commands\Server\Controllers\CPO\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ocpi\Support\Server\Controllers\Controller;

/**
 * Route only used in Versions details to give an endpoint for this Module to the CPO.
 */
class GetMockedController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return $this->ocpiSuccessResponse();
    }
}
