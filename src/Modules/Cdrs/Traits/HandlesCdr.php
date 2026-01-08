<?php

namespace Ocpi\Modules\Cdrs\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Ocpi\Helpers\PaginatedCollection;
use Ocpi\Models\Cdrs\Cdr;
use Ocpi\Models\Locations\LocationEvse;
use Ocpi\Models\Sessions\Session;
use Ocpi\Modules\Cdrs\Events;
use Ocpi\Modules\Cdrs\Factories\CdrFactory;
use Ocpi\Modules\Cdrs\Objects\CdrCollection;

trait HandlesCdr
{
    /**
     * @param string $cdrId
     * @return Cdr|null
     */
    private function cdrSearch(string $cdrId): ?Cdr
    {
        return Cdr::query()
            ->where('cdr_id', $cdrId)
            ->first();
    }

    /**
     * @param int $partyRoleId
     * @param Carbon|null $dateFrom
     * @param Carbon|null $dateTo
     * @param int $offset
     * @param int $limit
     * @return CdrCollection
     */
    private function list(
        int $partyRoleId,
        ?Carbon $dateFrom = null,
        ?Carbon $dateTo = null,
        int $offset = 0,
        int $limit = PaginatedCollection::DEFAULT_PER_PAGE,
    ): CdrCollection {
        $perPage = $limit;
        $page = ($offset / $limit) + 1;
        $collection = Cdr::query()
            ->where('party_role_id', $partyRoleId)
            ->when(null !== $dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('updated_at', '>=', $dateFrom);
            })
            ->when(null !== $dateTo, function ($query) use ($dateTo) {
                $query->whereDate('updated_at', '<=', $dateTo);
            })
            ->paginate(perPage: $perPage, page: $page);
        return CdrFactory::fromCollection($collection);
    }

    /**
     * @param array $payload
     * @param int $partyRoleId
     * @param LocationEvse $locationEvse
     * @return Cdr|null
     */
    private function createCdr(array $payload, int $partyRoleId, LocationEvse $locationEvse): ?Cdr
    {
        if (($payload['id'] ?? null) === null) {
            return null;
        }

        $cdr = new Cdr;
        $cdr->fill([
            'party_role_id' => $partyRoleId,
            'location_evse_id' => $locationEvse->id,
            'location_id' => $locationEvse->location_id,
            'cdr_id' => $payload['id'],
            'object' => $payload,
        ]);
        //find session with $payload['session_id']
        $sessionId = $payload['session_id'] ?? null;
        if (null !== $sessionId) {
            /** @var Session|null $session */
            $session = Session::query()->where('session_id', $sessionId)->first();
            $cdr->session_id = $session?->id;
        }

        if (false === $cdr->save()) {
            return null;
        }

        Events\EMSP\CdrCreated::dispatch($cdr->id);

        return $cdr;
    }

    /**
     * @param Cdr $cdr
     * @return string
     */
    private function cdrRoute(Cdr $cdr): string
    {
        return route(
            config('ocpi.server.routing.emsp.name_prefix')
            . Str::replace('.', '_', Context::get('ocpi_version'))
            . '.cdrs',
            [
                'cdr_id' => $cdr->cdr_id,
            ]
        );
    }
}
