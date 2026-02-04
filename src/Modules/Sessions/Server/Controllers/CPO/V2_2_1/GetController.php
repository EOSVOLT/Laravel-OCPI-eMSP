<?php

namespace Ocpi\Modules\Sessions\Server\Controllers\CPO\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Sessions\Traits\HandlesSession;
use Ocpi\Support\Client\Requests\SessionListRequest;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    use HandlesSession;

    public function __invoke(SessionListRequest $request): JsonResponse
    {
        /** @var PartyToken $token */
        $token = PartyToken::query()->find(Context::get('token_id'));

        $dateFrom = Carbon::createFromTimeString($request->input('date_from'));
        $dateTo = (null !== $request->input('date_to')) ? Carbon::createFromTimeString(
            $request->input('date_to')
        ) : null;
        $offset = $request->input('offset');
        $limit = $request->input('limit');
        $collection = $this->sessionSearch(
            $token->party_role_id,
            $dateFrom,
            $dateTo,
            $offset,
            $limit
        );

        $data = $collection->pluck('session_details');

        return $this->ocpiSuccessPaginateResponse($data, $offset, $limit, $collection->getTotalResults(), 'sessions');
    }
}
