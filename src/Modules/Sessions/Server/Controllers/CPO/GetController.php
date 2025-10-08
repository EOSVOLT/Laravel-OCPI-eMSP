<?php

namespace Ocpi\Modules\Sessions\Server\Controllers\CPO;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Ocpi\Modules\Sessions\Traits\HandlesSession;
use Ocpi\Support\Client\Requests\ListRequest;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    use HandlesSession;

    public function __invoke(ListRequest $request): JsonResponse
    {
        $dateFrom = Carbon::createFromTimeString($request->input('date_from'));
        $dateTo = Carbon::createFromTimeString($request->input('date_to'));
        $offset = $request->input('offset');
        $limit = $request->input('limit');
        $collection = $this->sessionSearch(
            $dateFrom,
            $dateTo,
            $offset,
            $limit
        );

        $data = $collection->pluck('session_details');

        return $this->ocpiSuccessPaginateResponse($data, $offset, $limit, $collection->getTotalResults(), 'sessions');
    }
}
