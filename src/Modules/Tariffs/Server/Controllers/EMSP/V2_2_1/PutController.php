<?php

namespace Ocpi\Modules\Tariffs\Server\Controllers\EMSP\V2_2_1;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\PartyRole;
use Ocpi\Models\Tariffs\Tariff;
use Ocpi\Modules\Tariffs\Repositories\TariffRepository;
use Ocpi\Modules\Tariffs\Traits\HandlesTariff;
use Ocpi\Support\Server\Controllers\Controller;
use Throwable;

class PutController extends Controller
{
    use HandlesTariff;

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
     *
     * @return JsonResponse
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function upsert(
        string $countryCode,
        string $partyCode,
        string $externalId,
        Request $request
    ): JsonResponse {
        $partyRoleId = Context::get('party_role_id');
        $partyRole = PartyRole::find($partyRoleId);
        $data = $request->json()->all();
        $tariff = Tariff::query()
            ->where('party_id', $partyRole->party_id)
            ->where('external_id', $externalId)
            ->first();
        if (null === $tariff) {
            $this->tariffCreate($partyRole, $externalId, $data);
        } else {
            $this->tariffReplace($tariff, $data);
        }

        return $this->ocpiSuccessResponse();
    }
}