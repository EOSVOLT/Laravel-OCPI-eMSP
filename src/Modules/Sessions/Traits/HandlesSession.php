<?php

namespace Ocpi\Modules\Sessions\Traits;

use Illuminate\Support\Carbon;
use Ocpi\Models\Locations\LocationConnector;
use Ocpi\Models\Sessions\Session;
use Ocpi\Modules\Sessions\Events;
use Ocpi\Modules\Sessions\Factories\SessionFactory;
use Ocpi\Modules\Sessions\Objects\SessionCollection;
use Ocpi\Support\Enums\SessionStatus;

trait HandlesSession
{
    /**
     * @param string $externalSessionId
     * @param int $partyRoleId
     * @return Session|null
     * @todo return object instead.
     */
    private function sessionById(string $externalSessionId, int $partyRoleId): ?Session
    {
        return Session::query()
            ->where('session_id', $externalSessionId)
            ->where('party_role_id', $partyRoleId)
            ->first();
    }

    /**
     * @param Carbon $dateFrom
     * @param Carbon $dateTo
     * @param int $offset
     * @param int $limit
     * @return SessionCollection
     */
    private function sessionSearch(Carbon $dateFrom, Carbon $dateTo, int $offset, int $limit): SessionCollection
    {
        $perPage = $limit;
        $page = ($offset / $limit) + 1;
        $collection = Session::query()->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->paginate(
                perPage: $perPage,
                page: $page,
            );
        return SessionFactory::fromCollection($collection);
    }

    /**
     * @param array $payload
     * @param int $partyRoleId
     * @param string $externalSessionId
     * @param LocationConnector|null $connector
     * @return bool
     */
    private function sessionCreate(
        array $payload,
        int $partyRoleId,
        string $externalSessionId,
        ?LocationConnector $connector
    ): bool {
        if (($payload['id'] ?? null) === null || $payload['id'] !== $externalSessionId) {
            return false;
        }
        $status = SessionStatus::tryFrom($payload['status']);
        $session = new Session;
        $session->fill([
            'party_role_id' => $partyRoleId,
            'location_id' => $connector?->evse->location_id,
            'location_evse_id' => $connector?->evse->id,
            'location_connector_id' => $connector?->id,
            'session_id' => $externalSessionId,
            'object' => $payload,
            'status' => $status,
        ]);

        if (!$session->save()) {
            return false;
        }

        Events\EMSP\SessionCreated::dispatch($session->id);

        return true;
    }

    /**
     * @param array $payload
     * @param Session $session
     * @return bool
     */
    private function sessionReplace(array $payload, Session $session): bool
    {
        if (null === ($payload['id'] ?? null) || $payload['id'] !== $session->session_id) {
            return false;
        }

        $session->object = $payload;

        if (false === $session->save()) {
            return false;
        }

        Events\EMSP\SessionReplaced::dispatch($session->party_role_id, $session->id, $payload);

        return true;
    }

    /**
     * @param array $payload
     * @param Session $session
     * @return bool
     */
    private function sessionObjectUpdate(array $payload, Session $session): bool
    {
        foreach ($payload as $field => $value) {
            $session->object[$field] = $value;
        }

        if (!$session->save()) {
            return false;
        }

        Events\EMSP\SessionUpdated::dispatch($session->party_role_id, $session->id, $payload);

        return true;
    }
}
