<?php

namespace Ocpi\Modules\Sessions\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Ocpi\Models\Sessions\Session;
use Ocpi\Modules\Sessions\Events;
use Ocpi\Modules\Sessions\Factories\SessionFactory;
use Ocpi\Modules\Sessions\Objects\SessionCollection;
use Ocpi\Support\Enums\SessionStatus;

trait HandlesSession
{
    /**
     * @todo return object instead.
     * @param string $session_id
     * @param int $party_role_id
     * @return Session|null
     */
    private function sessionById(string $session_id, int $party_role_id): ?Session
    {
        return Session::query()
            ->where('id', $session_id)
            ->where('party_role_id', $party_role_id)
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
        $collection = Session::query()->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->offset($offset)
            ->limit($limit)
            ->get();
        return SessionFactory::fromCollection($collection);
    }

    /**
     * @param array $payload
     * @param int $party_role_id
     * @param string $session_id
     * @param string|null $location_id
     * @return bool
     */
    private function sessionCreate(array $payload, int $party_role_id, string $session_id, ?string $location_id): bool
    {
        if (($payload['id'] ?? null) === null || $payload['id'] !== $session_id) {
            return false;
        }
        $status = SessionStatus::tryFrom($payload['status']);
        $session = new Session;
        $session->fill([
            'party_role_id' => $party_role_id,
            'location_id' => $location_id,
            'session_id' => $session_id,
            'object' => $payload,
            'status' => $status,
        ]);

        if (! $session->save()) {
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
        if (($payload['id'] ?? null) === null || $payload['id'] !== $session->id) {
            return false;
        }

        $session->object = $payload;

        if (! $session->save()) {
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

        if (! $session->save()) {
            return false;
        }

        Events\EMSP\SessionUpdated::dispatch($session->party_role_id, $session->id, $payload);

        return true;
    }
}
