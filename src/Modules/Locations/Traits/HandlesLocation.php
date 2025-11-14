<?php

namespace Ocpi\Modules\Locations\Traits;

use Illuminate\Support\Facades\Validator;
use Ocpi\Models\Locations\Location as LocationModel;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Locations\Factories\LocationFactory;
use Ocpi\Modules\Locations\Objects\ConnectorCollection;
use Ocpi\Modules\Locations\Objects\Evse;
use Ocpi\Modules\Locations\Objects\EvseCollection;
use Ocpi\Modules\Locations\Objects\Location;

trait HandlesLocation
{
    public function searchByExternalId(PartyRole $partyRole, string $externalId): ?Location
    {
        $location = LocationModel::query()
            ->where('party_id', $partyRole->party_id)
            ->where('external_id', $externalId)
            ->withTrashed()
            ->first();
        if ($location === null) {
            return null;
        }

        return LocationFactory::fromModel($location);
    }

    public function evsesCreate(array $payload, Location $location): EvseCollection
    {
        return new EvseCollection();
    }

    public function connector(array $payload, Evse $evse): ConnectorCollection
    {
        return new ConnectorCollection();
    }

    private function locationValidator(array $payload): Validator
    {
        return Validator::make($payload, [
            'country_code' => 'required|string|size:2',
            'party_id' => 'required|string|size:3',
            'id' => 'required|string|max:36',
            'publish' => 'required|boolean',
            'publish_allowed_to ' => 'nullable|array'

        ]);
    }
//
//    private function locationCreate(array $payload, PartyRole $partyRole, string $locationExternalId): ?Location
//    {
//        if (($payload['id'] ?? null) === null || $payload['id'] !== $locationExternalId) {
//            return null;
//        }
//        $party = $partyRole->party;
//        $payloadEvseList = $payload['evses'] ?? null;
//        unset($payload['evses']);
//
//        $location = new LocationModel();
//        $location->fill([
//            'external_id' => $locationExternalId,
//            'party_id' => $party->id,
//            'publish' => $payload['publish']
//        ]);
//        $location->object = $payload;
//        $location->save();
//
//        LocationCreated::dispatch($party_role_id, $location_id, $payload);
//
//        // Create EVSEs.
//        foreach (($payloadEvseList ?? []) as $payloadEvse) {
//            if (! $this->evseCreate(
//                payload: $payloadEvse,
//                location: $location,
//                evse_uid: ($payloadEvse['uid'] ?? null),
//                updateLocation: false
//            )) {
//                return false;
//            }
//        }
//
//        return true;
//    }
//
//    private function locationReplace(array $payload, Location $location): bool
//    {
//        if (($payload['id'] ?? null) === null || $payload['id'] !== $location->id) {
//            return false;
//        }
//
//        if ($location->trashed()) {
//            $location->restore();
//        }
//
//        $payloadEvseList = $payload['evses'] ?? null;
//        unset($payload['evses']);
//
//        $location->object = $payload;
//        $location->save();
//
//        Events\LocationReplaced::dispatch($location->party_role_id, $location->id, $payload);
//
//        // Create or Replace EVSEs, Connectors.
//        foreach (($payloadEvseList ?? []) as $payloadEvse) {
//            $locationEvse = $location
//                ->evsesWithTrashed
//                ->where('uid', $payloadEvse['uid'])
//                ->first();
//
//            if ($locationEvse === null) {
//                if (! $this->evseCreate(
//                    payload: $payloadEvse,
//                    location: $location,
//                    evse_uid: ($payloadEvse['uid'] ?? null),
//                    updateLocation: false,
//                )) {
//                    return false;
//                }
//            } else {
//                if (! $this->evseReplace(
//                    payload: $payloadEvse,
//                    locationEvse: $locationEvse,
//                    updateLocation: false,
//                )) {
//                    return false;
//                }
//            }
//        }
//
//        // Delete missing EVSEs.
//        $locationEvseToDeleteList = $location
//            ->evsesWithTrashed
//            ->whereNotIn('uid', collect($payloadEvseList)->pluck('uid')->toArray());
//        if ($locationEvseToDeleteList->count() > 0) {
//            $locationEvseToDeleteList->each(function (LocationEvse $locationEvseToDelete) {
//                $this->evseObjectUpdate(
//                    payload: ['status' => 'REMOVED'],
//                    locationEvse: $locationEvseToDelete,
//                );
//            });
//        }
//
//        return true;
//    }
//
//    private function locationObjectUpdate(array $payload, Location $location): bool
//    {
//        foreach ($payload as $field => $value) {
//            $location->object[$field] = $value;
//        }
//
//        if (! $location->save()) {
//            return false;
//        }
//
//        Events\LocationUpdated::dispatch($location->party_role_id, $location->id, $payload);
//
//        return true;
//    }
//
//    private function evseSearch(int $party_role_id, string $location_id, string $evse_uid, bool $withTrashed = false): ?LocationEvse
//    {
//        return LocationEvse::query()
//            ->with($withTrashed === true
//                ? ['locationWithTrashed']
//                : ['location']
//            )
//            ->when($withTrashed === true, function ($query) {
//                return $query->withTrashed();
//            })
//            ->whereHas($withTrashed === true
//                ? 'locationWithTrashed'
//                : 'location',
//                function ($query) use ($party_role_id, $location_id) {
//                    $query->partyRole($party_role_id)
//                        ->where('id', $location_id);
//                })
//            ->where('uid', $evse_uid)
//            ->first();
//    }
//
//    private function evseCreate(array $payload, Location $location, ?string $evse_uid, bool $updateLocation = true): bool
//    {
//        if (($payload['uid'] ?? null) === null || $payload['uid'] !== $evse_uid) {
//            return false;
//        }
//
//        // Create EVSE.
//        $locationEvse = new LocationEvse;
//        $locationEvse->fill([
//            'location_emsp_id' => $location->emsp_id,
//            'uid' => $payload['uid'],
//        ]);
//
//        $payloadConnectorList = $payload['connectors'] ?? null;
//        unset($payload['connectors']);
//
//        $locationEvse->object = $payload;
//        $locationEvse->save();
//
//        Events\LocationEvseCreated::dispatch($location->party_role_id, $location->id, $locationEvse->uid, $payload);
//
//        // Update Location.
//        if ($updateLocation) {
//            if (($payload['status'] ?? null) !== 'REMOVED' && $locationEvse->locationWithTrashed->trashed()) {
//                $locationEvse->locationWithTrashed->restore();
//
//                Events\LocationRestored::dispatch($location->party_role_id, $location->id);
//            }
//            if (($payload['last_updated'] ?? null) !== null) {
//                $locationEvse->locationWithTrashed->object['last_updated'] = $payload['last_updated'];
//                $locationEvse->locationWithTrashed->save();
//            }
//        }
//
//        // Create EVSE Connectors.
//        foreach (($payloadConnectorList ?? []) as $payloadConnector) {
//            if (($payloadConnector['id'] ?? null) === null) {
//                return false;
//            }
//
//            $locationConnector = new LocationConnector;
//            $locationConnector->fill([
//                'location_evse_emsp_id' => $locationEvse->emsp_id,
//                'id' => $payloadConnector['id'],
//            ]);
//
//            $locationConnector->object = $payloadConnector;
//            $locationConnector->save();
//
//            Events\LocationConnectorCreated::dispatch($location->party_role_id, $location->id, $locationEvse->uid, $payloadConnector['id'], $payloadConnector);
//        }
//
//        return true;
//    }
//
//    private function evseReplace(array $payload, LocationEvse $locationEvse, bool $updateLocation = true): bool
//    {
//        if (($payload['uid'] ?? null) === null || $payload['uid'] !== $locationEvse->uid) {
//            return false;
//        }
//
//        // Delete EVSE.
//        if (($payload['status'] ?? null) === 'REMOVED') {
//            $locationEvse->delete();
//            $this->connectorUpdate(
//                $locationEvse->connectors,
//                $locationEvse,
//                [
//                    'deleted_at' => now(),
//                ]
//            );
//
//            Events\LocationEvseRemoved::dispatch($locationEvse->location?->party_role_id, $locationEvse->location?->id, $locationEvse->uid);
//
//            if ($locationEvse->locationWithTrashed->evses()->count() === 0) {
//                $locationEvse->locationWithTrashed->delete();
//
//                Events\LocationRemoved::dispatch($locationEvse->location?->party_role_id, $locationEvse->location?->id);
//            }
//
//            return true;
//        }
//
//        // Replace EVSE.
//        $payloadConnectorList = $payload['connectors'] ?? null;
//        unset($payload['connectors']);
//
//        // No Connector => Delete EVSE.
//        if (count($payloadConnectorList ?? []) === 0) {
//            $locationEvse->delete();
//
//            Events\LocationEvseRemoved::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id, $locationEvse->uid);
//
//            $this->connectorUpdate(
//                $locationEvse->connectors,
//                $locationEvse,
//                [
//                    'deleted_at' => now(),
//                ]
//            );
//
//            if ($locationEvse->locationWithTrashed->evses()->count() === 0) {
//                $locationEvse->locationWithTrashed->delete();
//
//                Events\LocationRemoved::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id);
//            }
//
//            return true;
//        }
//
//        if ($locationEvse->trashed()) {
//            $locationEvse->restore();
//
//            Events\LocationEvseRestored::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id, $locationEvse->uid);
//        }
//        $locationEvse->object = $payload;
//        $locationEvse->save();
//
//        Events\LocationEvseReplaced::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id, $locationEvse->uid, $payload);
//
//        // Touch Location.
//        if ($updateLocation) {
//            if ($locationEvse->locationWithTrashed->trashed()) {
//                $locationEvse->locationWithTrashed->restore();
//
//                Events\LocationRestored::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id);
//            }
//            if (($payload['last_updated'] ?? null) !== null) {
//                $locationEvse->locationWithTrashed->object['last_updated'] = $payload['last_updated'];
//                $locationEvse->locationWithTrashed->save();
//            }
//        }
//
//        $locationEvse->loadMissing('connectorsWithTrashed');
//
//        // Create or replace EVSE Connectors.
//        foreach (($payloadConnectorList ?? []) as $payloadConnector) {
//            if (($payloadConnector['id'] ?? null) === null) {
//                return false;
//            }
//
//            $locationConnectorAttributes = [];
//
//            $locationConnector = $locationEvse
//                ->connectorsWithTrashed
//                ->where('id', $payloadConnector['id'])
//                ->first();
//            if ($locationConnector === null) {
//                $locationConnector = new LocationConnector;
//                $locationConnector->fill([
//                    'location_evse_emsp_id' => $locationEvse?->emsp_id,
//                    'id' => $payloadConnector['id'],
//                    'object' => null,
//                ]);
//                $locationConnector->save();
//
//                Events\LocationConnectorCreated::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id, $locationEvse->uid, $payloadConnector['id'], $payloadConnector);
//            } else {
//                if ($locationConnector->trashed()) {
//                    $locationConnectorAttributes['deleted_at'] = null;
//                }
//
//                Events\LocationConnectorReplaced::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id, $locationEvse->uid, $payloadConnector['id'], $payloadConnector);
//            }
//
//            $locationConnectorAttributes['object'] = $payloadConnector;
//            $this->connectorUpdate(
//                $locationConnector?->id,
//                $locationEvse,
//                $locationConnectorAttributes
//            );
//        }
//
//        // Delete missing EVSE Connectors.
//        $payloadConnectorIdList = collect($payloadConnectorList)->pluck('id')->toArray();
//        $this->connectorUpdate(
//            $locationEvse->connectorsWithTrashed->whereNotIn('id', $payloadConnectorIdList),
//            $locationEvse,
//            [
//                'deleted_at' => now(),
//            ]
//        );
//
//        return true;
//    }
//
//    private function evseObjectUpdate(array $payload, LocationEvse $locationEvse): bool
//    {
//        // Delete EVSE.
//        if (($payload['status'] ?? null) === 'REMOVED') {
//            $locationEvse->delete();
//
//            Events\LocationEvseRemoved::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id, $locationEvse->uid);
//
//            $this->connectorUpdate(
//                $locationEvse->connectors,
//                $locationEvse,
//                [
//                    'deleted_at' => now(),
//                ]
//            );
//
//            if ($locationEvse->locationWithTrashed->evses()->count() === 0) {
//                $locationEvse->locationWithTrashed->delete();
//
//                Events\LocationRemoved::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id);
//            }
//
//            return true;
//        }
//
//        foreach ($payload as $field => $value) {
//            $locationEvse->object[$field] = $value;
//        }
//
//        // Touch Location.
//        if (($payload['last_updated'] ?? null) !== null) {
//            $locationEvse->locationWithTrashed->object['last_updated'] = $payload['last_updated'];
//            if (! $locationEvse->locationWithTrashed->save()) {
//                return false;
//            }
//        }
//
//        if (! $locationEvse->save()) {
//            return false;
//        }
//
//        Events\LocationEvseUpdated::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id, $locationEvse->uid, $payload);
//
//        if (($payload['status'] ?? null) !== null && $locationEvse->trashed()) {
//            $locationEvse->restore();
//
//            Events\LocationEvseRestored::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id, $locationEvse->uid);
//
//            $this->connectorUpdate(
//                $locationEvse->connectorsWithTrashed,
//                $locationEvse,
//                [
//                    'deleted_at' => null,
//                ]
//            );
//
//            if ($locationEvse->locationWithTrashed->trashed()) {
//                $locationEvse->locationWithTrashed->restore();
//
//                Events\LocationRestored::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id);
//            }
//        }
//
//        return true;
//    }
//
//    private function connectorCreateOrReplace(array $payload, ?string $connector_id, LocationEvse $locationEvse): bool
//    {
//        if (($payload['id'] ?? null) === null || $payload['id'] !== $connector_id) {
//            return false;
//        }
//
//        $locationEvse->loadMissing('connectorsWithTrashed');
//
//        $locationConnector = $locationEvse
//            ->connectorsWithTrashed
//            ->where('id', $connector_id)
//            ->first();
//
//        $locationConnectorAttributes = [];
//
//        if ($locationConnector === null) {
//            $locationConnector = new LocationConnector;
//            $locationConnector->fill([
//                'location_evse_emsp_id' => $locationEvse->emsp_id,
//                'id' => $connector_id,
//                'object' => null,
//            ]);
//            $locationConnector->save();
//
//            Events\LocationConnectorCreated::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id, $locationEvse->uid, $connector_id, $payload);
//        } else {
//            if ($locationConnector->trashed()) {
//                $locationConnectorAttributes['deleted_at'] = null;
//            }
//
//            Events\LocationConnectorReplaced::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id, $locationEvse->uid, $connector_id, $payload);
//        }
//
//        $locationConnectorAttributes['object'] = $payload;
//        $this->connectorUpdate(
//            $connector_id,
//            $locationEvse,
//            $locationConnectorAttributes
//        );
//
//        // Touch EVSE, Location.
//        if (($payload['last_updated'] ?? null) !== null) {
//            $locationEvse->object['last_updated'] = $payload['last_updated'];
//            $locationEvse->save();
//
//            $locationEvse->locationWithTrashed->object['last_updated'] = $payload['last_updated'];
//            $locationEvse->locationWithTrashed->save();
//        }
//
//        return true;
//    }
//
//    private function connectorUpdate(string|Collection $connectorIdOrCollection, LocationEvse $locationEvse, array $attributes): void
//    {
//        LocationConnector::query()
//            ->withTrashed()
//            ->where('location_evse_emsp_id', $locationEvse->emsp_id)
//            ->when(
//                is_string($connectorIdOrCollection), function (Builder $query) use ($connectorIdOrCollection) {
//                $query->where('id', $connectorIdOrCollection);
//            }, function (Builder $query) use ($connectorIdOrCollection) {
//                $query->whereIn('id', $connectorIdOrCollection->pluck('id'));
//            })
//            ->update($attributes);
//    }
//
//    private function connectorObjectUpdate(array $payload, LocationConnector $locationConnector, LocationEvse $locationEvse): bool
//    {
//        foreach ($payload as $field => $value) {
//            $locationConnector->object[$field] = $value;
//        }
//
//        // Touch EVSE, Location.
//        if (($payload['last_updated'] ?? null) !== null) {
//            $locationEvse->object['last_updated'] = $payload['last_updated'];
//            $locationEvse->save();
//
//            $locationEvse->locationWithTrashed->object['last_updated'] = $payload['last_updated'];
//            $locationEvse->locationWithTrashed->save();
//        }
//
//        $this->connectorUpdate(
//            $locationConnector->id,
//            $locationEvse,
//            [
//                'object' => $locationConnector->object->toArray(),
//            ]
//        );
//
//        Events\LocationConnectorUpdated::dispatch($locationEvse->locationWithTrashed?->party_role_id, $locationEvse->locationWithTrashed?->id, $locationEvse->uid, $locationConnector->id, $payload);
//
//        return true;
//    }
}
