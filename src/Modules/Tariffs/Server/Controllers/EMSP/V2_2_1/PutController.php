<?php

namespace Ocpi\Modules\Tariffs\Server\Controllers\EMSP\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\Tariff\Tariff;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Modules\Tariffs\Events\EMSP\TariffCreated;
use Ocpi\Modules\Tariffs\Events\EMSP\TariffRemoved;
use Ocpi\Modules\Tariffs\Events\EMSP\TariffReplaced;
use Ocpi\Modules\Tariffs\Repositories\TariffRepository;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class PutController extends Controller
{
    use HandlesLocation;

    /**
     * @param TariffRepository $tariffRepository
     */
    public function __construct(private readonly TariffRepository $tariffRepository)
    {
    }

    /**
     * @param string $countryCode
     * @param string $partyCode
     * @param string $externalId
     * @param Request $request
     * @return JsonResponse
     */
    public function upsert(
        string $countryCode,
        string $partyCode,
        string $externalId,
        Request $request
    ): JsonResponse {
        $partyId = Context::get('party_role_party_id');
        $data = $request->json()->all();
        $exist = Tariff::query()
            ->where('party_id', $partyId)
            ->where('external_id', $externalId)
            ->exists();
        $tariff = $this->tariffRepository->createOrUpdateFromArray($partyId, $data);
        if (true === $exist) {
            TariffReplaced::dispatch($tariff);
        } else {
            TariffCreated::dispatch($tariff);
        }

        return $this->ocpiSuccessResponse();
    }

    public function delete(
        string $countryCode,
        string $partyCode,
        string $externalId,
        Request $request
    ) {
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