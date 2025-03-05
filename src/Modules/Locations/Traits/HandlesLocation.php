<?php

namespace Ocpi\Modules\Locations\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Ocpi\Models\Locations\Location;
use Ocpi\Models\Locations\LocationConnector;
use Ocpi\Models\Locations\LocationEvse;
use Ocpi\Modules\Locations\Events;

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

        Events\LocationCreated::dispatch($party_role_id, $location_id, $payload);

        // Create EVSEs.
        foreach (($payloadEvseList ?? []) as $payloadEvse) {
            if (! $this->evseCreate(
                payload: $payloadEvse,
                party_role_id: $party_role_id,
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

        Events\LocationReplaced::dispatch($location->party_role_id, $location->id, $payload);

        // Create or Replace EVSEs, Connectors.
        foreach (($payloadEvseList ?? []) as $payloadEvse) {
            $locationEvse = $location
                ->withTrashedEvses
                ->where('uid', $payloadEvse['uid'])
                ->first();

            if ($locationEvse === null) {
                if (! $this->evseCreate(
                    payload: $payloadEvse,
                    party_role_id: $party_role_id,
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

        if (! $location->save()) {
            return false;
        }

        Events\LocationUpdated::dispatch($location->party_role_id, $location->id, $payload);

        return true;
    }

    private function evseSearch(string $evse_uid, int $party_role_id, bool $withTrashed = false): ?LocationEvse
    {
        return LocationEvse::query()
            ->when($withTrashed === true, function ($query) {
                return $query->withTrashed();
            })
            ->where('uid', $evse_uid)
            ->where('party_role_id', $party_role_id)
            ->first();
    }

    private function evseCreate(array $payload, int $party_role_id, string $location_id, ?string $evse_uid, bool $updateLocation = true): bool
    {
        if (($payload['uid'] ?? null) === null || $payload['uid'] !== $evse_uid) {
            return false;
        }

        // Create EVSE.
        $locationEvse = new LocationEvse;
        $locationEvse->fill([
            'party_role_id' => $party_role_id,
            'location_id' => $location_id,
            'uid' => $payload['uid'],
        ]);

        $payloadConnectorList = $payload['connectors'] ?? null;
        unset($payload['connectors']);

        $locationEvse->object = $payload;
        $locationEvse->save();

        Events\LocationEvseCreated::dispatch($locationEvse->withTrashedLocation?->party_role_id, $evse_uid, $payload);

        // Update Location.
        if ($updateLocation) {
            if (($payload['status'] ?? null) !== 'REMOVED' && $locationEvse->withTrashedLocation->trashed()) {
                $locationEvse->withTrashedLocation->restore();

                Events\LocationRestored::dispatch($locationEvse->withTrashedLocation?->party_role_id, $location_id);
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
                'party_role_id' => $party_role_id,
                'location_evse_composite_id' => $locationEvse?->composite_id,
                'id' => $payloadConnector['id'],
            ]);

            $locationConnector->object = $payloadConnector;
            $locationConnector->save();

            Events\LocationConnectorCreated::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->uid, $payloadConnector['id'], $payloadConnector);
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
            $this->connectorUpdate(
                $locationEvse->connectors,
                $locationEvse,
                [
                    'deleted_at' => now(),
                ]
            );

            Events\LocationEvseRemoved::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->uid);

            if ($locationEvse->withTrashedLocation->evses()->count() === 0) {
                $locationEvse->withTrashedLocation->delete();

                Events\LocationRemoved::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->withTrashedLocation?->id);
            }

            return true;
        }

        // Replace EVSE.
        $payloadConnectorList = $payload['connectors'] ?? null;
        unset($payload['connectors']);

        // No Connectors => Delete EVSE.
        if (count($payloadConnectorList ?? []) === 0) {
            $locationEvse->delete();

            Events\LocationEvseRemoved::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->uid);

            // $locationEvse->connectors()->delete();
            $this->connectorUpdate(
                $locationEvse->connectors,
                $locationEvse,
                [
                    'deleted_at' => now(),
                ]
            );

            if ($locationEvse->withTrashedLocation->evses()->count() === 0) {
                $locationEvse->withTrashedLocation->delete();

                Events\LocationRemoved::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->withTrashedLocation?->id);
            }

            return true;
        }

        if ($locationEvse->trashed()) {
            $locationEvse->restore();

            Events\LocationEvseRestored::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->uid);
        }
        $locationEvse->object = $payload;
        $locationEvse->save();

        Events\LocationEvseReplaced::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->uid, $payload);

        // Touch Location.
        if ($updateLocation) {
            if ($locationEvse->withTrashedLocation->trashed()) {
                $locationEvse->withTrashedLocation->restore();

                Events\LocationRestored::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->withTrashedLocation?->id);
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
                ->where('party_role_id', $locationEvse->party_role_id)
                ->first();
            if ($locationConnector === null) {
                $locationConnector = new LocationConnector;
                $locationConnector->fill([
                    'party_role_id' => $locationEvse?->party_role_id,
                    'location_evse_composite_id' => $locationEvse?->composite_id,
                    'id' => $payloadConnector['id'],
                    'object' => null,
                ]);
                $locationConnector->save();

                Events\LocationConnectorCreated::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->uid, $payloadConnector['id'], $payloadConnector);
            } else {
                if ($locationConnector->trashed()) {
                    // $locationConnector->restore();
                    $locationConnectorAttributes['deleted_at'] = null;
                }

                Events\LocationConnectorReplaced::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->uid, $payloadConnector['id'], $payloadConnector);
            }

            // $locationConnector->object = $payloadConnector;
            // $locationConnector->save();
            $locationConnectorAttributes['object'] = $payloadConnector;
            $this->connectorUpdate(
                $locationConnector?->id,
                $locationEvse,
                $locationConnectorAttributes
            );
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
            ]
        );

        return true;
    }

    private function evseObjectUpdate(array $payload, LocationEvse $locationEvse): bool
    {
        // Delete EVSE.
        if (($payload['status'] ?? null) === 'REMOVED') {
            $locationEvse->delete();

            Events\LocationEvseRemoved::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->uid);

            // $locationEvse->connectors()->delete();
            $this->connectorUpdate(
                $locationEvse->connectors,
                $locationEvse,
                [
                    'deleted_at' => now(),
                ]
            );

            if ($locationEvse->withTrashedLocation->evses()->count() === 0) {
                $locationEvse->withTrashedLocation->delete();

                Events\LocationRemoved::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->withTrashedLocation?->id);
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

        if (! $locationEvse->save()) {
            return false;
        }

        Events\LocationEvseUpdated::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->uid, $payload);

        if (($payload['status'] ?? null) !== null && $locationEvse->trashed()) {
            $locationEvse->restore();

            Events\LocationEvseRestored::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->uid);

            $this->connectorUpdate(
                $locationEvse->withTrashedConnectors,
                $locationEvse,
                [
                    'deleted_at' => null,
                ]
            );

            if ($locationEvse->withTrashedLocation->trashed()) {
                $locationEvse->withTrashedLocation->restore();

                Events\LocationRestored::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->withTrashedLocation?->id);
            }
        }

        return true;
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
            ->where('party_role_id', $locationEvse->party_role_id)
            ->first();

        $locationConnectorAttributes = [];

        if ($locationConnector === null) {
            $locationConnector = new LocationConnector;
            $locationConnector->fill([
                'party_role_id' => $locationEvse->party_role_id,
                'location_evse_composite_id' => $locationEvse?->composite_id,
                'id' => $connector_id,
                'object' => null,
            ]);
            $locationConnector->save();

            Events\LocationConnectorCreated::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->uid, $connector_id, $payload);
        } else {
            if ($locationConnector->trashed()) {
                // $locationConnector->restore();
                $locationConnectorAttributes['deleted_at'] = null;
            }

            Events\LocationConnectorReplaced::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->uid, $connector_id, $payload);
        }

        // $locationConnector->object = $payload;
        // $locationConnector->save();
        $locationConnectorAttributes['object'] = $payload;
        $this->connectorUpdate(
            $connector_id,
            $locationEvse,
            $locationConnectorAttributes
        );

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
            ->where('party_role_id', $locationEvse?->party_role_id)
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

        $this->connectorUpdate(
            $locationConnector->id,
            $locationEvse,
            [
                'object' => $locationConnector->object->toArray(),
            ]
        );

        Events\LocationConnectorUpdated::dispatch($locationEvse->withTrashedLocation?->party_role_id, $locationEvse->uid, $locationConnector->id, $payload);

        return true;
    }
}
