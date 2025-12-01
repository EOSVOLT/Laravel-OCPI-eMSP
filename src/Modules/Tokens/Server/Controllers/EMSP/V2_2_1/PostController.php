<?php

namespace Ocpi\Modules\Tokens\Server\Controllers\EMSP\V2_2_1;

use Illuminate\Http\JsonResponse;
use Ocpi\Modules\Tokens\Server\Requests\AuthorizeRequest;
use Ocpi\Support\Server\Controllers\Controller;

class PostController extends Controller
{
    public function authorize(AuthorizeRequest $request): JsonResponse
    {

        return $this->ocpiSuccessResponse();
    }
}