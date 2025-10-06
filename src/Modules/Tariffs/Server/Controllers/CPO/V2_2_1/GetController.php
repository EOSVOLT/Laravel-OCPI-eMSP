<?php

namespace Ocpi\Modules\Tariffs\Server\Controllers\CPO\V2_2_1;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\Tariff\Tariff;
use Ocpi\Modules\Credentials\Object\Party;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Client\Requests\ListRequest;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    use HandlesLocation;

    /**
     * GET /tariffs
     */
    public function list(ListRequest $request): JsonResponse
    {
        /** @var Party $party */
        $party = Context::getHidden('party');
        $offset = $request->input('offset');
        $limit = $request->input('limit');
        $tariff = Tariff::query()
            ->where('party_id', $party->getId())
            ->when($request->input('date_from'), function ($query) use ($request) {
                $query->where('updated_at', '>=', Carbon::parse($request->input('date_from')));
            })
            ->when($request->input('date_to'), function ($query) use ($request) {
                $query->where('updated_at', '<', Carbon::parse($request->input('date_to')));
            })
            ->orderBy('created_at')
            ->offset($offset)
            ->limit($limit)
            ->get();
        return $this->ocpiSuccessPaginateResponse(
            $tariff?->toArray() ?? [],
            $offset,
            $limit,
            $tariff->count(),
            self::getLocationPath(Context::get('ocpi_version')),
        );
    }
}
