<?php

namespace Ocpi\Modules\Tokens\Server\Controllers\EMSP\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ocpi\Support\Server\Controllers\Controller;

class GetMockedController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return $this->ocpiSuccessResponse();
    }
}