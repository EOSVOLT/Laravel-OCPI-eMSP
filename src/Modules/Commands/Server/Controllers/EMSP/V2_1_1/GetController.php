<?php

namespace Ocpi\Modules\Commands\Server\Controllers\EMSP\V2_1_1;

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
