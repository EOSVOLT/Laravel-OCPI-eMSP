<?php

namespace Ocpi\Modules\Locations\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Ocpi\Models\Locations\Location;
use Ocpi\Models\Locations\LocationConnector;
use Ocpi\Models\Locations\LocationEvse;

trait HandlesLocation
{
    private function locationSearch(string $location_id, int $party_role_id, bool $withTrashed = false): ?Location
    {
        return Location::query()
            ->withTrashed()
            ->with($withTrashed === true
                ? ['withTrashedEvses.withTrashedConnectors']
                : ['evses.connectors']
            )
            ->where('id', $location_id)
            ->where('party_role_id', $party_role_id)
            ->first();
    }

    private function locationCreate(array $payload, int $party_role_id, string $location_id): bool
    {
        if (($payload['id'] ?? null) === null || $payload['id'] !== $location_id) {
            return false;
        }

        // Create Location.
        $location = new Location;
        $location->fill([
            'party_role_id' => $party_role_id,
            'id' => $location_id,
        ]);

        $payloadEvseList = $payload['evses'] ?? null;
        unset($payload['evses']);

        $location->object = $payload;
        $location->save();

        // Create EVSEs.
        foreach (($payloadEvseList ?? []) as $payloadEvse) {
            if (! $this->evseCreate(
                payload: $payloadEvse,
                location_id: $location->id,
                evse_uid: ($payloadEvse['uid'] ?? null),
                updateLocation: false
            )) {
                return false;
            }
        }

        return true;
    }

    private function locationReplace(array $payload, Location $location): bool
    {
        if (($payload['id'] ?? null) === null || $payload['id'] !== $location->id) {
            return false;
        }

        if ($location->trashed()) {
            $location->restore();
        }

        $payloadEvseList = $payload['evses'] ?? null;
        unset($payload['evses']);

        $location->object = $payload;
        $location->save();

        // Create or Replace EVSEs, Connectors.
        foreach (($payloadEvseList ?? []) as $payloadEvse) {
            $locationEvse = $location
                ->withTrashedEvses
                ->where('uid', $payloadEvse['uid'])
                ->first();

            if ($locationEvse === null) {
                if (! $this->evseCreate(
                    payload: $payloadEvse,
                    location_id: $location->id,
                    evse_uid: ($payloadEvse['uid'] ?? null),
                    updateLocation: false,
                )) {
                    return false;
                }
            } else {
                if (! $this->evseReplace(
                    payload: $payloadEvse,
                    locationEvse: $locationEvse,
                    updateLocation: false,
                )) {
                    return false;
                }
            }
        }

        return true;
    }

    private function locationObjectUpdate(array $payload, Location $location): bool
    {
        foreach ($payload as $field => $value) {
            $location->object[$field] = $value;
        }

        return $location->save();
    }

    private function evseSearch(string $evse_uid, int $party_role_id, bool $withTrashed = false): ?LocationEvse
    {
        return LocationEvse::query()
            ->when($withTrashed === true, function ($query) {
                return $query->withTrashed();
            })
            ->where('uid', $evse_uid)
            ->whereHas($withTrashed ? 'withTrashedLocation' : 'location', function (Builder $query) use ($party_role_id) {
                $query->where('party_role_id', $party_role_id);
            })
            ->first();
    }

    private function evseCreate(array $payload, string $location_id, ?string $evse_uid, bool $updateLocation = true): bool
    {
        if (($payload['uid'] ?? null) === null || $payload['uid'] !== $evse_uid) {
            return false;
        }

        // Create EVSE.
        $locationEvse = new LocationEvse;
        $locationEvse->fill([
            'location_id' => $location_id,
            'uid' => $payload['uid'],
        ]);

        $payloadConnectorList = $payload['connectors'] ?? null;
        unset($payload['connectors']);

        $locationEvse->object = $payload;
        $locationEvse->save();

        // Update Location.
        if ($updateLocation) {
            if (($payload['status'] ?? null) !== 'REMOVED' && $locationEvse->withTrashedLocation->trashed()) {
                $locationEvse->withTrashedLocation->restore();
            }
            if (($payload['last_updated'] ?? null) !== null) {
                $locationEvse->withTrashedLocation->object['last_updated'] = $payload['last_updated'];
                $locationEvse->withTrashedLocation->save();
            }
        }

        // Create EVSE Connectors.
        foreach (($payloadConnectorList ?? []) as $payloadConnector) {
            if (($payloadConnector['id'] ?? null) === null) {
                return false;
            }

            $locationConnector = new LocationConnector;
            $locationConnector->fill([
                'location_evse_composite_id' => $locationEvse?->composite_id,
                'id' => $payloadConnector['id'],
            ]);

            $locationConnector->object = $payloadConnector;
            $locationConnector->save();
        }

        return true;
    }

    private function evseReplace(array $payload, LocationEvse $locationEvse, bool $updateLocation = true): bool
    {
        if (($payload['uid'] ?? null) === null || $payload['uid'] !== $locationEvse->uid) {
            return false;
        }

        // Delete EVSE.
        if (($payload['status'] ?? null) === 'REMOVED') {
            $locationEvse->delete();
            // $locationEvse->connectors()->delete();
            $this->connectorUpdate($locationEvse->connectors, $locationEvse, [
                'deleted_at' => now(),
            ]);

            if ($locationEvse->withTrashedLocation->evses()->count() === 0) {
                $locationEvse->withTrashedLocation->delete();
            }

            return true;
        }

        // Replace EVSE.
        $payloadConnectorList = $payload['connectors'] ?? null;
        unset($payload['connectors']);

        // No Connectors => Delete EVSE.
        if (count($payloadConnectorList ?? []) === 0) {
            $locationEvse->delete();
            // $locationEvse->connectors()->delete();
            $this->connectorUpdate($locationEvse->connectors, $locationEvse, [
                'deleted_at' => now(),
            ]);

            if ($locationEvse->withTrashedLocation->evses()->count() === 0) {
                $locationEvse->withTrashedLocation->delete();
            }

            return true;
        }

        if ($locationEvse->trashed()) {
            $locationEvse->restore();
        }
        $locationEvse->object = $payload;
        $locationEvse->save();

        // Touch Location.
        if ($updateLocation) {
            if ($locationEvse->withTrashedLocation->trashed()) {
                $locationEvse->withTrashedLocation->restore();
            }
            if (($payload['last_updated'] ?? null) !== null) {
                $locationEvse->withTrashedLocation->object['last_updated'] = $payload['last_updated'];
                $locationEvse->withTrashedLocation->save();
            }
        }

        $locationEvse->loadMissing('withTrashedConnectors');

        // Create or replace EVSE Connectors.
        foreach (($payloadConnectorList ?? []) as $payloadConnector) {
            if (($payloadConnector['id'] ?? null) === null) {
                return false;
            }

            $locationConnectorAttributes = [];

            $locationConnector = $locationEvse
                ->withTrashedConnectors
                ->where('id', $payloadConnector['id'])
                ->first();
            if ($locationConnector === null) {
                $locationConnector = new LocationConnector;
                $locationConnector->fill([
                    'location_evse_composite_id' => $locationEvse?->composite_id,
                    'id' => $payloadConnector['id'],
                    'object' => null,
                ]);
                $locationConnector->save();
            } else {
                if ($locationConnector->trashed()) {
                    // $locationConnector->restore();
                    $locationConnectorAttributes['deleted_at'] = null;
                }
            }

            // $locationConnector->object = $payloadConnector;
            // $locationConnector->save();
            $locationConnectorAttributes['object'] = $payloadConnector;
            $this->connectorUpdate($locationConnector?->id, $locationEvse, $locationConnectorAttributes);
        }

        // Delete missing EVSE Connectors.
        $payloadConnectorIdList = collect($payloadConnectorList)->pluck('id')->toArray();
        // $locationEvse
        //     ->withTrashedConnectors
        //     ->whereNotIn('id', $payloadConnectorIdList)
        //     ->each(function (LocationConnector $locationConnector) use ($locationEvse) {
        //         $locationConnector->delete();
        //     });
        $this->connectorUpdate(
            $locationEvse->withTrashedConnectors->whereNotIn('id', $payloadConnectorIdList),
            $locationEvse,
            [
                'deleted_at' => now(),
            ]);

        return true;
    }

    private function evseObjectUpdate(array $payload, LocationEvse $locationEvse): bool
    {
        // Delete EVSE.
        if (($payload['status'] ?? null) === 'REMOVED') {
            $locationEvse->delete();
            // $locationEvse->connectors()->delete();
            $this->connectorUpdate($locationEvse->connectors, $locationEvse, [
                'deleted_at' => now(),
            ]);

            if ($locationEvse->withTrashedLocation->evses()->count() === 0) {
                $locationEvse->withTrashedLocation->delete();
            }
        }

        foreach ($payload as $field => $value) {
            $locationEvse->object[$field] = $value;
        }

        // Touch Location.
        if (($payload['last_updated'] ?? null) !== null) {
            $locationEvse->withTrashedLocation->object['last_updated'] = $payload['last_updated'];
            if (! $locationEvse->withTrashedLocation->save()) {
                return false;
            }
        }

        return $locationEvse->save();
    }

    private function connectorCreateOrReplace(array $payload, ?string $connector_id, LocationEvse $locationEvse): bool
    {
        if (($payload['id'] ?? null) === null || $payload['id'] !== $connector_id) {
            return false;
        }

        $locationEvse->loadMissing('withTrashedConnectors');

        $locationConnector = $locationEvse
            ->withTrashedConnectors
            ->where('id', $connector_id)
            ->first();

        $locationConnectorAttributes = [];

        if ($locationConnector === null) {
            $locationConnector = new LocationConnector;
            $locationConnector->fill([
                'location_evse_composite_id' => $locationEvse?->composite_id,
                'id' => $connector_id,
                'object' => null,
            ]);
            $locationConnector->save();
        } else {
            if ($locationConnector->trashed()) {
                // $locationConnector->restore();
                $locationConnectorAttributes['deleted_at'] = null;
            }
        }

        // $locationConnector->object = $payload;
        // $locationConnector->save();
        $locationConnectorAttributes['object'] = $payload;
        $this->connectorUpdate($connector_id, $locationEvse, $locationConnectorAttributes);

        // Touch EVSE, Location.
        if (($payload['last_updated'] ?? null) !== null) {
            $locationEvse->object['last_updated'] = $payload['last_updated'];
            $locationEvse->save();

            $locationEvse->withTrashedLocation->object['last_updated'] = $payload['last_updated'];
            $locationEvse->withTrashedLocation->save();
        }

        return true;
    }

    private function connectorUpdate(string|Collection $connectorIdOrCollection, LocationEvse $locationEvse, array $attributes): void
    {
        LocationConnector::query()
            ->withTrashed()
            ->where('location_evse_composite_id', $locationEvse?->composite_id)
            ->when(
                is_string($connectorIdOrCollection), function (Builder $query) use ($connectorIdOrCollection) {
                    $query->where('id', $connectorIdOrCollection);
                }, function (Builder $query) use ($connectorIdOrCollection) {
                    $query->whereIn('id', $connectorIdOrCollection->pluck('id'));
                })
            ->update($attributes);
    }

    private function connectorObjectUpdate(array $payload, LocationConnector $locationConnector, LocationEvse $locationEvse): bool
    {
        foreach ($payload as $field => $value) {
            $locationConnector->object[$field] = $value;
        }

        // Touch EVSE, Location.
        if (($payload['last_updated'] ?? null) !== null) {
            $locationEvse->object['last_updated'] = $payload['last_updated'];
            $locationEvse->save();

            $locationEvse->withTrashedLocation->object['last_updated'] = $payload['last_updated'];
            $locationEvse->withTrashedLocation->save();
        }

        $this->connectorUpdate($locationConnector->id, $locationEvse, [
            'object' => $locationConnector->object->toArray(),
        ]);

        return true;
    }
}
