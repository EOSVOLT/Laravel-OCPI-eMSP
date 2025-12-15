<?php

namespace Ocpi\Modules\Cdrs\Server\Controllers\CPO\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Context;
use Ocpi\Helpers\PaginatedCollection;
use Ocpi\Models\Party;
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
        $partyCode = Context::get('party_code');

        $party = Party::with(['roles'])->where('code', $partyCode)->first();
        if ($party === null || $party->roles->count() === 0) {
            return $this->ocpiServerErrorResponse(
                statusCode: OcpiServerErrorCode::PartyApiUnusable,
                statusMessage: 'Party not found.',
                httpCode: 405,
            );
        }

        $partyRoleId = $party->roles->first()->id;
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', PaginatedCollection::DEFAULT_PER_PAGE);
        $cdr = $this->list(
            partyRoleId: $partyRoleId,
            dateFrom: (null !== $request->input('date_from')) ? Carbon::createFromTimeString($request->input('date_from')) : null,
            dateTo: (null !== $request->input('date_to')) ? Carbon::createFromTimeString($request->input('date_to')) : null,
            offset: $offset,
            limit: $limit,
        );

        if (0 === $cdr->count()) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::InvalidParameters,
                statusMessage: 'Unknown CDR.',
            );
        }

        return $this->ocpiSuccessPaginateResponse(
            $cdr->pluck('cdr_details'),
            $offset,
            $limit,
            $cdr->getTotalResults(),
            'cdrs',
        );
    }
}
