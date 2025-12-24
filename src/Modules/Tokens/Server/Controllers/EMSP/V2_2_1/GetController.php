<?php

namespace Ocpi\Modules\Tokens\Server\Controllers\EMSP\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\PartyRole;
use Ocpi\Models\Tokens\CommandToken;
use Ocpi\Modules\Tokens\Factories\CommandTokenFactory;
use Ocpi\Support\Client\Requests\ListRequest;
use Ocpi\Support\Enums\Role;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    public function __invoke(ListRequest $request): JsonResponse
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $offset = $request->input('offset');
        $limit = $request->input('limit');
        /** @var PartyRole $partyRole */
        $partyRole = Context::getHidden('party_role');
        $data = [];
        $totalResults = 0;
        if (Role::EMSP === $partyRole?->role) {
            $query = CommandToken::query()
                ->where('party_role_id', $partyRole->id)
                ->when(null !== $dateFrom, function ($query) use ($dateFrom) {
                    $query->where('updated_at', '>=', $dateFrom);
                })
                ->when(null !== $dateTo, function ($query) use ($dateTo) {
                    $query->where('updated_at', '<', $dateTo);
                })
                ->orderBy('updated_at');

            // Get total count before applying limit/offset
            $totalResults = $query->clone()->count();

            $items = $query
                ->when(null !== $limit, function ($query) use ($limit) {
                    $query->limit($limit);
                })
                ->when(null !== $offset, function ($query) use ($offset) {
                    $query->offset($offset);
                })
                ->get();
            $data = CommandTokenFactory::fromCollection($items)->toArray();
        }


        return $this->ocpiSuccessPaginateResponse(
            $data,
            $offset,
            $limit,
            $totalResults,
            'tokens'
        );
    }
}