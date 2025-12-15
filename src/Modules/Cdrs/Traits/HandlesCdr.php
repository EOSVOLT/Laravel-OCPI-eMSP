<?php

namespace Ocpi\Modules\Cdrs\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Ocpi\Helpers\PaginatedCollection;
use Ocpi\Models\Cdrs\Cdr;
use Ocpi\Modules\Cdrs\Events;
use Ocpi\Modules\Cdrs\Factories\CdrFactory;
use Ocpi\Modules\Cdrs\Objects\CdrCollection;

trait HandlesCdr
{
    private function cdrSearch(?string $id = null, ?string $cdrId = null, ?int $partyRoleId = null): ?Cdr
    {
        return Cdr::query()
            ->when(
                $id !== null,
                function ($query) use ($id) {
                    $query->where('id', $id);
                },
                function ($query) use ($cdrId) {
                    $query->where('id', $cdrId);
                }
            )
            ->when(
                $partyRoleId !== null,
                function ($query) use ($partyRoleId) {
                    $query->where('party_role_id', $partyRoleId);
                }
            )
            ->first();
    }

    private function list(
        int $partyRoleId,
        ?Carbon $dateFrom = null,
        ?Carbon $dateTo = null,
        int $offset = 0,
        int $limit = PaginatedCollection::DEFAULT_PER_PAGE,
    ): CdrCollection {
        $collection = Cdr::query()
            ->where('party_role_id', $partyRoleId)
            ->when( null !== $dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('updated_at', '>=', $dateFrom);
            })
            ->when( null !== $dateTo, function ($query) use ($dateTo) {
                $query->whereDate('updated_at', '<=', $dateTo);
            })
            ->offset($offset)
            ->limit($limit)
            ->get();
        return CdrFactory::fromCollection($collection);
    }

    private function cdrCreate(array $payload, int $party_role_id, ?string $location_evse_id): ?Cdr
    {
        if (($payload['id'] ?? null) === null) {
            return null;
        }

        $cdr = new Cdr;
        $cdr->fill([
            'party_role_id' => $party_role_id,
            'location_evse_id' => $location_evse_id,
            'id' => $payload['id'],
            'object' => $payload,
        ]);

        if (!$cdr->save()) {
            return null;
        }

        Events\EMSP\CdrCreated::dispatch($party_role_id, $cdr->id, $payload);

        return $cdr;
    }

    private function cdrRoute(Cdr $cdr): string
    {
        return route(
            config('ocpi.server.routing.emsp.name_prefix')
            . Str::replace('.', '_', Context::get('ocpi_version'))
            . '.cdrs',
            [
                'cdr_emsp_id' => $cdr->emsp_id,
            ]
        );
    }
}
