<?php

namespace Ocpi\Modules\Locations\Server\Controllers\CPO;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\Locations\Location;
use Ocpi\Models\Locations\LocationConnector;
use Ocpi\Models\Locations\LocationEvse;
use Ocpi\Modules\Locations\Factories\ConnectorFactory;
use Ocpi\Modules\Locations\Factories\EvseFactory;
use Ocpi\Modules\Locations\Factories\LocationFactory;
use Ocpi\Modules\Locations\Resources\ConnectorResource;
use Ocpi\Modules\Locations\Resources\EvseResource;
use Ocpi\Modules\Locations\Resources\LocationResource;
use Ocpi\Modules\Locations\Resources\LocationResourceList;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    use HandlesLocation;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function list(
        Request $request,
    ): JsonResponse {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);
        $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : Carbon::now(
        )->startOfDay();
        $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : Carbon::now();
        $party = Context::getHidden('party');
        $page = (int)floor($offset / $limit) + 1;
        $location = Location::query()
            ->with(['party.role_cpo'])
            ->where('party_id', $party->getId())
            ->where('updated_at', '>=', $dateFrom->toDateTimeString()) //inclusive
            ->where('updated_at', '<', $dateTo->toDateTimeString()) //exclusive
            ->where('publish', true)
            ->withHasValidEvses()
            ->paginate(
                perPage: $limit,
                page: $page
            );
        $locationObj = LocationFactory::fromPaginator($location);
        return $location->count() > 0
            ? $this->ocpiSuccessPaginateResponse(
                new LocationResourceList($locationObj)->toArray(),
                $location->currentPage(),
                $location->perPage(),
                $location->total(),
                self::getLocationPath(Context::get('ocpi_version'))
            )
            : $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::UnknownLocation,
                statusMessage: 'Unknown Location',
                httpCode: 404,
            );
    }

    /**
     * @param string $locationId
     *
     * @return JsonResponse
     */
    public function location(string $locationId): JsonResponse
    {
        $party = Context::getHidden('party');
        $location = Location::query()
            ->with(['party.role_cpo', 'evses.connectors'])
            ->where('party_id', $party->getId())
            ->where('external_id', $locationId)
            ->first();
        if (null !== $location) {
            return $this->ocpiSuccessResponse(new LocationResource(LocationFactory::fromModel($location)));
        }
        return $this->ocpiClientErrorResponse(
            statusCode: OcpiClientErrorCode::UnknownLocation,
            statusMessage: 'Unknown Location',
            httpCode: 404,
        );
    }

    /**
     * @param string $locationId
     * @param string $evseUid
     *
     * @return JsonResponse
     */
    public function evse(string $locationId, string $evseUid): JsonResponse
    {
        $party = Context::getHidden('party');
        $evse = LocationEvse::query()
            ->with(['location.party.role_cpo', 'connectors'])
            ->whereHas('location', function (Builder $query) use ($locationId, $party) {
                $query->where('external_id', $locationId)
                    ->where('party_id', $party->getId());
            })
            ->where('uid', $evseUid)
            ->first();
        if (null !== $evse) {
            return $this->ocpiSuccessResponse(new EvseResource(EvseFactory::fromModel($evse)));
        }
        return $this->ocpiClientErrorResponse(
            statusMessage: 'Evse not found',
            httpCode: 404,
        );
    }

    /**
     * @param string $locationId
     * @param string $evseUid
     * @param string $connectorId
     *
     * @return JsonResponse
     */
    public function connector(string $locationId, string $evseUid, string $connectorId): JsonResponse
    {
        $party = Context::getHidden('party');
        $connector = LocationConnector::query()
            ->with(['evse.location'])
            ->whereHas('evse', function (Builder $query) use ($locationId, $evseUid, $party) {
                $query->validEvse();
                $query->whereHas('location', function (Builder $query) use ($locationId, $party) {
                    $query->where('party_id', $party->getId());
                });
            })
            ->first();
        if (null !== $connector) {
            return $this->ocpiSuccessResponse(new ConnectorResource(ConnectorFactory::fromModel($connector)));
        }
        return $this->ocpiClientErrorResponse(
            statusMessage: 'Connector not found',
            httpCode: 404,
        );
    }
}
