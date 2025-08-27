<?php

namespace Ocpi\Modules\Commands\Server\Controllers\CPO\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return $this->ocpiSuccessResponse();
    }
}
