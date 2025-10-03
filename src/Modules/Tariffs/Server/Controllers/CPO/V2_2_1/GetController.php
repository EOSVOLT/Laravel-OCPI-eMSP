<?php

namespace Ocpi\Modules\Tariffs\Server\Controllers\CPO\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\Tariff\Tariff;
use Ocpi\Modules\Credentials\Object\Party;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Server\Controllers\Controller;
use Ocpi\Support\Traits\PageConvertor;

class GetController extends Controller
{
    use HandlesLocation;
    use PageConvertor;
    /**
     * GET /tariffs
     */
    public function list(Request $request): JsonResponse
    {
        $offset = (int) $request->input('offset', 0);
        $limit = (int) $request->input('limit', 20);
        $page = self::fromOffset($offset, $limit);

        /** @var Party $party */
        $party = Context::getHidden('party');

        $tariff = Tariff::query()->where('party_id', $party->getId())
            ->offset($offset)
            ->limit($limit)
            ->paginate();

        return $this->ocpiSuccessPaginateResponse(
            $tariff,
            $page,
            $limit,
            $tariff->total(),
            self::getLocationPath(Context::get('ocpi_version')),
        );
    }
}
