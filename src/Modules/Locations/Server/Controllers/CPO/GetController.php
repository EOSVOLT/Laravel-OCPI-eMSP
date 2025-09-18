<?php

namespace Ocpi\Modules\Locations\Server\Controllers\CPO;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\Locations\Location;
use Ocpi\Modules\Locations\Enums\EvseStatus;
use Ocpi\Modules\Locations\Factories\LocationFactory;
use Ocpi\Modules\Locations\Resources\CPOGetLocationResourceList;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    use HandlesLocation;
    public CONST LOCATION_PATH = 'ocpi/cpo/2.2.1/locations';
    public function __invoke(
        Request $request,
    ): JsonResponse {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);
        $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : Carbon::now()->startOfDay();
        $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : Carbon::now();
        $party = Context::getHidden('party');
        $page = $offset > 0 ? (int)ceil($offset / $limit) + 1 : 1;
        $location = Location::query()
            ->with(['evses.connectors'])
            ->where('party_id', $party->id)
            ->where('updated_at', '>=', $dateFrom->toDateTimeString()) //inclusive
            ->where('updated_at', '<', $dateTo->toDateTimeString()) //exclusive
            ->where('publish', true)
            ->whereHas('evses', function (Builder $query) {
                $query->whereNotIn('status', [EvseStatus::REMOVED, EvseStatus::UNKNOWN]);
            })
            ->paginate(
                perPage: $limit,
                page: $page
            );
        $locationObj = LocationFactory::fromPaginator($location);
        return $location->count() > 0
            ? $this->ocpiSuccessPaginateResponse(
                (new CPOGetLocationResourceList($locationObj))->toArray(),
                $location->currentPage(),
                $location->perPage(),
                $location->total(),
                self::LOCATION_PATH
            )
            : $this->ocpiServerErrorResponse(
                statusMessage: 'Location not found',
                httpCode: 404,
            );
    }

    private function locationFormat()
    {
    }
}
