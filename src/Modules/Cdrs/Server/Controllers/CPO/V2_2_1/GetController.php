<?php

namespace Ocpi\Modules\Cdrs\Server\Controllers\CPO\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Context;
use Ocpi\Helpers\PaginatedCollection;
use Ocpi\Models\Party;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Cdrs\Traits\HandlesCdr;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Enums\OcpiServerErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    use HandlesCdr;

    public function __invoke(
        Request $request,
    ): JsonResponse {

        /** @var PartyToken $token */
        $token = PartyToken::query()->find(Context::get('token_id'));
        $partyRoleId = $token->party_role_id;
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', PaginatedCollection::DEFAULT_PER_PAGE);
        $cdr = $this->list(
            partyRoleId: $partyRoleId,
            dateFrom: (null !== $request->input('date_from')) ? Carbon::createFromTimeString($request->input('date_from')) : null,
            dateTo: (null !== $request->input('date_to')) ? Carbon::createFromTimeString($request->input('date_to')) : null,
            offset: $offset,
            limit: $limit,
        );

        return $this->ocpiSuccessPaginateResponse(
            $cdr->pluck('cdr_details'),
            $offset,
            $limit,
            $cdr->getTotalResults(),
            'cdrs',
        );
    }
}
