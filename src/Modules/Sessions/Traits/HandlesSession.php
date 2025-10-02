<?php

namespace Ocpi\Modules\Sessions\Traits;

use Ocpi\Models\Sessions\Session;
use Ocpi\Modules\Sessions\Events;
use Ocpi\Support\Enums\Invoker;
use Ocpi\Support\Enums\SessionStatus;

trait HandlesSession
{
    private function sessionSearch(string $session_id, int $party_role_id): ?Session
    {
        return Session::query()
            ->where('id', $session_id)
            ->where('party_role_id', $party_role_id)
            ->first();
    }

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
            'id' => $session_id,
            'object' => $payload,
            'status' => $status,
        ]);

        if (! $session->save()) {
            return false;
        }

        Events\SessionCreated::dispatch($session->id);

        return true;
    }

    private function sessionReplace(array $payload, Session $session): bool
    {
        if (($payload['id'] ?? null) === null || $payload['id'] !== $session->id) {
            return false;
        }

        $session->object = $payload;

        if (! $session->save()) {
            return false;
        }

        Events\SessionReplaced::dispatch($session->party_role_id, $session->id, $payload);

        return true;
    }

    private function sessionObjectUpdate(array $payload, Session $session): bool
    {
        foreach ($payload as $field => $value) {
            $session->object[$field] = $value;
        }

        if (! $session->save()) {
            return false;
        }

        Events\SessionUpdated::dispatch($session->party_role_id, $session->id, $payload);

        return true;
    }
}
