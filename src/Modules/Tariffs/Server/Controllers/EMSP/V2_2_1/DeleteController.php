<?php

namespace Ocpi\Modules\Tariffs\Server\Controllers\EMSP\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\Tariffs\Tariff;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Modules\Tariffs\Events\EMSP\TariffRemoved;
use Ocpi\Modules\Tariffs\Repositories\TariffRepository;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class DeleteController extends Controller
{
    use HandlesLocation;

    /**
     * @param TariffRepository $tariffRepository
     */
    public function __construct(private readonly TariffRepository $tariffRepository)
    {
    }

    public function delete(
        string $countryCode,
        string $partyCode,
        string $externalId,
        Request $request
    ): JsonResponse {
        $partyId = Context::get('party_role_party_id');
        $tariff = Tariff::query()
            ->where('party_id', $partyId)
            ->where('external_id', $externalId)
            ->first();
        if (null === $tariff) {
            return $this->ocpiClientErrorResponse(OcpiClientErrorCode::InvalidParameters, 'Tariff not found', 404);
        }
        $tariff->delete();
        TariffRemoved::dispatch($tariff);
        return $this->ocpiSuccessResponse();
    }
}