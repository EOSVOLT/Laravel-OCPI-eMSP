<?php

namespace Ocpi\Modules\Locations\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Ocpi\Models\Locations\Location;
use Ocpi\Models\Locations\Location as LocationModel;
use Ocpi\Models\Locations\LocationConnector;
use Ocpi\Models\Locations\LocationEvse;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Locations\Enums\ConnectorFormat;
use Ocpi\Modules\Locations\Enums\ConnectorType;
use Ocpi\Modules\Locations\Enums\EnergySourceCategory;
use Ocpi\Modules\Locations\Enums\EnvironmentalImpactCategory;
use Ocpi\Modules\Locations\Enums\EvseCapability;
use Ocpi\Modules\Locations\Enums\EvseStatus;
use Ocpi\Modules\Locations\Enums\Facility;
use Ocpi\Modules\Locations\Enums\ImageCategory;
use Ocpi\Modules\Locations\Enums\ParkingType;
use Ocpi\Modules\Locations\Enums\PowerType;
use Ocpi\Modules\Locations\Enums\TokenType;
use Ocpi\Modules\Locations\Events\EMSP\LocationConnectorCreated;
use Ocpi\Modules\Locations\Events\EMSP\LocationConnectorReplaced;
use Ocpi\Modules\Locations\Events\EMSP\LocationConnectorUpdated;
use Ocpi\Modules\Locations\Events\EMSP\LocationCreated;
use Ocpi\Modules\Locations\Events\EMSP\LocationEvseCreated;
use Ocpi\Modules\Locations\Events\EMSP\LocationEvseRemoved;
use Ocpi\Modules\Locations\Events\EMSP\LocationEvseReplaced;
use Ocpi\Modules\Locations\Events\EMSP\LocationEvseRestored;
use Ocpi\Modules\Locations\Events\EMSP\LocationEvseUpdated;
use Ocpi\Modules\Locations\Events\EMSP\LocationFullyCreated;
use Ocpi\Modules\Locations\Events\EMSP\LocationFullyReplaced;
use Ocpi\Modules\Locations\Events\EMSP\LocationRemoved;
use Ocpi\Modules\Locations\Events\EMSP\LocationReplaced;
use Ocpi\Modules\Locations\Events\EMSP\LocationRestored;
use Ocpi\Modules\Locations\Events\EMSP\LocationUpdated;

trait HandlesLocation
{
    public function searchLocation(PartyRole $partyRole, ?string $externalId, bool $withTrashed = true): ?Location
    {
        $location = LocationModel::query()
            ->where('party_id', $partyRole->party_id)
            ->when(null !== $externalId, function ($query) use ($externalId) {
                $query->where('external_id', $externalId);
            })
            ->withTrashed($withTrashed)
            ->first();
        if ($location === null) {
            return null;
        }

        return $location;
    }

    private function locationRules(): array
    {
        return [
            //Location object
            'country_code' => 'required|string|min:2|max:2',
            'party_id' => 'required|string|min:3|max:3',
            'id' => 'required|string|max:36',
            'publish' => 'required|boolean',

            'publish_allowed_to ' => 'nullable|array',
            'publish_allowed_to.uid' => 'nullable|string|max:36',
            'publish_allowed_to.type' => ['nullable', 'string', Rule::in(TokenType::stringCases())],
            'publish_allowed_to.visual_number' => 'nullable|string|max:64',
            'publish_allowed_to.issuer' => 'nullable|string|max:64',
            'publish_allowed_to.group_id' => 'nullable|string|max:36',

            'name' => 'nullable|string|max:255',
            'address' => 'required|string|max:45',
            'city' => 'required|string|max:45',
            'postal_code' => 'nullable|string|max:10',
            'state' => 'nullable|string|max:20',
            'country' => 'required|string|min:3|max:3',

            'coordinates' => 'required|array',
            'coordinates.latitude' => ['required', 'string', 'regex:/^-?[0-9]{1,2}\.[0-9]{5,7}$/'],
            'coordinates.longitude' => [
                'required',
                'string',
                'regex:/^-?[0-9]{1,3}\.[0-9]{5,7}$/',
            ],

            'related_locations' => 'nullable|array',
            'related_locations.latitude' => [
                'required_with:related_locations.longitude',
                'string',
                'regex:/^-?[0-9]{1,2}\.[0-9]{5,7}$/',
            ],
            'related_locations.longitude' => [
                'required_with:related_locations.latitude',
                'string',
                'regex:/^-?[0-9]{1,3}\.[0-9]{5,7}$/',
            ],
            'related_locations.name' => 'nullable|array',
            'related_locations.name.language' => 'required_with:related_locations.name.text,nullable|string|max:2',
            'related_locations.name.text' => 'required_with:related_locations.name.language,nullable|string|max:512',

            'parking_type' => ['nullable', 'string', Rule::in(ParkingType::stringCases())],


            'directions' => 'nullable|array',
            'directions.language' => 'required_with:directions.text|string|max:2',
            'directions.text' => 'required_with:directions.language|string|max:512',

            'operator' => 'nullable|array',
            'operator.name' => 'required_with:operator|string|max:100',
            'operator.website' => 'nullable|string|max:255',
            'operator.logo' => 'nullable|array',
            'operator.logo.url' => 'required_with:operator.logo.type|string|max:255',
            'operator.logo.thumbnail' => 'nullable|string|max:255',
            'operator.logo.category' => [
                'required_with:operator.logo.type',
                'string',
                Rule::in(ImageCategory::stringCases()),
            ],
            'operator.logo.type' => 'required_with:operator.logo.url|string|max:4',
            'operator.logo.width' => 'nullable|int|max:5',
            'operator.logo.height' => 'nullable|int|max:5',

            'suboperator' => 'nullable|array',
            'suboperator.name' => 'required_with:suboperator|string|max:100',
            'suboperator.website' => 'nullable|string|max:255',
            'suboperator.logo' => 'nullable|array',
            'suboperator.logo.url' => 'required_with:suboperator.logo|string|max:255',
            'suboperator.logo.thumbnail' => 'nullable|string|max:255',
            'suboperator.logo.category' => [
                'required_with:suboperator.logo',
                'string',
                Rule::in(ImageCategory::stringCases()),
            ],
            'suboperator.logo.type' => 'required_with:suboperator.logo|string|max:4',
            'suboperator.logo.width' => 'nullable|int|max:5',
            'suboperator.logo.height' => 'nullable|int|max:5',

            'owner' => 'nullable|array',
            'owner.name' => 'required_with:owner|string|max:100',
            'owner.website' => 'nullable|string|max:255',
            'owner.logo' => 'nullable|array',
            'owner.logo.url' => 'required_with:owner.logo|string|max:255',
            'owner.logo.thumbnail' => 'nullable|string|max:255',
            'owner.logo.category' => [
                'required_with:owner.logo',
                'string',
                Rule::in(ImageCategory::stringCases()),
            ],
            'owner.logo.type' => 'required_with:owner.logo|string|max:4',
            'owner.logo.width' => 'nullable|int|max:5',
            'owner.logo.height' => 'nullable|int|max:5',

            'facilities' => 'nullable|array|distinct',
            'facilities.*' => ['string', Rule::in(Facility::stringCases())],

            'time_zone' => 'required|string|max:255',

            'opening_times' => 'nullable|array',
            'opening_times.twentyfourseven' => 'required_with:opening_times|boolean',
            'opening_times.regular_hours' => 'nullable|array',
            'opening_times.regular_hours.*.weekday' => 'required|numeric|min:1|max:7',
            'opening_times.regular_hours.*.period_begin' => [
                'required',
                'string',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
            ],
            'opening_times.regular_hours.*.period_end' => ['required', 'string', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],

            'opening_times.exceptional_opening' => 'nullable|array',
            'opening_times.exceptional_opening.*.period_begin' => [
                'required',
                'string',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
            ],
            'opening_times.exceptional_opening.*.period_end' => [
                'required',
                'string',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
            ],

            'opening_times.exceptional_closing' => 'nullable|array',
            'opening_times.exceptional_closing.*.period_begin' => [
                'required',
                'string',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
            ],
            'opening_times.exceptional_closing.*.period_end' => [
                'required',
                'string',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
            ],

            'charging_when_closed' => 'nullable|boolean', // default true
            'images' => 'nullable|array',
            'images.*.url' => 'required|string|max:255',
            'images.*.thumbnail' => 'nullable|string|max:255',
            'images.*.category' => [
                'required',
                'string',
                Rule::in(ImageCategory::stringCases()),
            ],
            'images.*.type' => 'required|string|max:4',
            'images.*.width' => 'nullable|int|max:5',
            'images.*.height' => 'nullable|int|max:5',

            'energy_mix' => 'nullable|array',
            'energy_mix.is_green_energy' => 'required_with:energy_mix|boolean',
            'energy_mix.energy_sources' => 'nullable|array',
            'energy_mix.energy_sources.*.source' => [
                'required',
                'string',
                Rule::in(EnergySourceCategory::stringCases()),
            ],
            'energy_mix.energy_sources.*.percentage' => 'required|numeric|min:0|max:100',

            'energy_mix.environ_impact' => 'nullable|array',
            'energy_mix.environ_impact.*.category' => [
                'required',
                'string',
                Rule::in(EnvironmentalImpactCategory::stringCases()),
            ],
            'energy_mix.environ_impact.*.amount' => 'required|decimal:0,4',
            'energy_mix.supplier_name' => 'nullable|string|max:64',
            'energy_mix.energy_product_name' => 'nullable|string|max:64',

            'last_updated' => [
                'required',
                'string',
                'regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,3})?Z$/',
            ],

        ];
    }

    private function evseRules(bool $isFromLocation = true): array
    {
        $prefix = $isFromLocation ? 'evses.*.' : '';
        $fromLocationRule = [];
        if ($isFromLocation) {
            $fromLocationRule = ['evses' => 'nullable|array'];
        }
        return $fromLocationRule + [
                // EVSE object
                $prefix . 'uid' => 'required|string|max:36',
                $prefix . 'evse_id' => 'required|string|max:48',
                $prefix . 'status' => ['required', 'string', Rule::in(EvseStatus::stringCases())],

                $prefix . 'status_schedule ' => 'nullable|array',
                $prefix . 'status_schedule.*.period_begin' => [
                    'required',
                    'string',
                    'regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,3})?Z$/',
                ],
                $prefix . 'status_schedule.*.period_end' => [
                    'nullable',
                    'string',
                    'regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,3})?Z$/',
                ],

                $prefix . 'capabilities' => 'nullable|array',
                $prefix . 'capabilities.*' => ['string', Rule::in(EvseCapability::stringCases())],

                $prefix . 'floor_level' => 'nullable|string|max:4',
                $prefix . 'coordinated' => 'nullable|array',
                $prefix . 'coordinated.latitude' => [
                    'required_with:' . $prefix . 'coordinated',
                    'string',
                    'regex:/^-?[0-9]{1,2}\.[0-9]{5,7}$/',
                ],
                $prefix . 'coordinated.longitude' => [
                    'required_with:' . $prefix . 'coordinated',
                    'string',
                    'regex:/^-?[0-9]{1,3}\.[0-9]{5,7}$/',
                ],
                $prefix . 'physical_reference' => 'nullable|string|max:16',

                $prefix . 'images' => 'nullable|array',
                $prefix . 'images.*.url' => 'required|string|max:255',
                $prefix . 'images.*.thumbnail' => 'nullable|string|max:255',
                $prefix . 'images.*.category' => [
                    'required',
                    'string',
                    Rule::in(ImageCategory::stringCases()),
                ],
                $prefix . 'images.*.type' => 'required|string|max:4',
                $prefix . 'images.*.width' => 'nullable|int|max:5',
                $prefix . 'images.*.height' => 'nullable|int|max:5',

                $prefix . 'last_updated' => [
                    'required',
                    'string',
                    'regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,3})?Z$/',
                ],
                // End EVSE object
            ];
    }

    private function connectorRules(bool $isFromEvse = true): array
    {
        $prefix = $isFromEvse ? 'connectors.*.' : '';
        $fromEvseRule = [];
        if (true === $isFromEvse) {
            $fromEvseRule = [$prefix . 'connectors' => 'required|array|min:1'];
        }
        return $fromEvseRule + [
                // Connector object
                $prefix . 'id' => 'required|string|max:36',
                $prefix . 'standard' => ['required', 'string', Rule::in(ConnectorType::stringCases())],
                $prefix . 'format' => ['required', 'string', Rule::in(ConnectorFormat::stringCases())],
                $prefix . 'power_type' => ['required', 'string', Rule::in(PowerType::stringCases())],
                $prefix . 'max_voltage' => 'required|numeric|min:0',
                $prefix . 'max_amperage' => 'required|numeric|min:0',
                $prefix . 'max_electric_power' => 'nullable|numeric|min:0',
                $prefix . 'tariff_ids' => 'nullable|array',

                $prefix . 'term_and_conditions' => 'nullable|array',
                $prefix . 'last_updated' => [
                    'required',
                    'string',
                    'regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,3})?Z$/',
                ],
                // End connector object
            ];
    }

    private function locationCreate(PartyRole $partyRole, string $locationExternalId, array $payload): bool
    {
        $party = $partyRole->party;
        $isPublished = $payload['publish'];
        $updatedAt = $payload['last_updated'];
        $payloadEvseList = $payload['evses'] ?? [];
        unset($payload['publish'], $payload['last_updated'], $payload['evses']);
        $location = new LocationModel();
        $location->fill([
            'external_id' => $locationExternalId,
            'party_id' => $party->id,
            'publish' => $isPublished,
            'object' => $payload,
            'updated_at' => $updatedAt,
        ]);
        $location->object = $payload;
        $location->save();
        $location->refresh();
        if (true === empty($payloadEvseList)) {
            LocationCreated::dispatch($location->id);//evese null as alway
            return true;
        }

        // Create EVSEs.
        foreach ($payloadEvseList as $payloadEvse) {
            $evseUid = $payloadEvse['uid'];
            if (!$this->evseCreate(
                $location,
                $evseUid,
                $payloadEvse,
                updateLocation: false,
                dispatchEvent: false
            )) {
                return false;
            }
        }
        LocationFullyCreated::dispatch($location->id);
        return true;
    }

//
    private function locationReplace(Location $location, array $payload): bool
    {
        if ($location->trashed()) {
            $location->restore();
        }

        $payloadEvseList = $payload['evses'] ?? [];
        $lastUpdated = $payload['last_updated'] ?? Carbon::now();
        $isPublish = $payload['publish'] ?? false;
        unset($payload['evses'], $payload['last_updated'], $payload['publish']);

        $location->publish = $isPublish;
        $location->updated_at = $lastUpdated;
        $location->object = $payload;
        $location->save();
        $location->refresh();
        if (true === empty($payloadEvseList)) {
            LocationReplaced::dispatch($location->id);
            return true;
        }

        // Create or Replace EVSEs, Connectors.
        foreach ($payloadEvseList as $payloadEvse) {
            $evseUid = $payloadEvse['uid'] ?? null;
            unset($payloadEvse['uid']);
            $locationEvse = $location
                ->evsesWithTrashed
                ->where('uid', $evseUid)
                ->first();

            if (null === $locationEvse) {
                if (!$this->evseCreate(
                    $location,
                    $evseUid,
                    $payloadEvse,
                    updateLocation: false,
                    dispatchEvent: false
                )) {
                    return false;
                }
            } else {
                if (!$this->evseReplace(
                    $locationEvse,
                    $payloadEvse,
                    updateLocation: false,
                    dispatchEvent: false
                )) {
                    return false;
                }
            }
        }

        // Delete missing EVSEs.
        $locationEvseToDeleteList = $location
            ->evsesWithTrashed
            ->whereNotIn('uid', collect($payloadEvseList)->pluck('uid')->toArray());
        if ($locationEvseToDeleteList->count() > 0) {
            $locationEvseToDeleteList->each(function (LocationEvse $locationEvseToDelete) {
                $this->eveseDelete($locationEvseToDelete, false);
            });
        }
        LocationFullyReplaced::dispatch($location->id);
        return true;
    }

    private function locationObjectUpdate(array $payload, Location $location): bool
    {
        unset($payload['evses'], $payload['last_updated'], $payload['publish']);
        foreach ($payload as $field => $value) {
            $location->object[$field] = $value;
        }

        if (!$location->save()) {
            return false;
        }

        LocationUpdated::dispatch($location->id);

        return true;
    }

    private function evseSearch(int $partyId, string $locationExternalId, string $evseUid): ?LocationEvse
    {
        return LocationEvse::query()
            ->with('connectors')
            ->withWhereHas(
                'location',
                function ($query) use ($partyId, $locationExternalId) {
                    $query->where('party_id', $partyId)
                        ->where('external_id', $locationExternalId);
                }
            )
            ->where('uid', $evseUid)
            ->withTrashed()
            ->first();
    }

    private function evseCreate(
        Location $location,
        string $evseUid,
        array $payload,
        bool $updateLocation = true,
        bool $dispatchEvent = true
    ): LocationEvse {
        // Create EVSE.
        $locationEvse = new LocationEvse;
        $locationEvse->fill([
            'location_id' => $location->id,
            'uid' => $evseUid,
        ]);

        $payloadConnectorList = $payload['connectors'] ?? null;
        $locationEvse->status = EvseStatus::tryFrom($payload['status']) ?? EvseStatus::UNKNOWN;
        $locationEvse->updated_at = $payload['last_updated'] ?? now();
        unset($payload['connectors'], $payload['status'], $payload['uid']);
        $locationEvse->object = $payload;
        $locationEvse->save();
        $locationEvse->refresh();
        if (true === $dispatchEvent) {
            LocationEvseCreated::dispatch($locationEvse->id);
        }

        // Update Location.
        if (true === $updateLocation) {
            if (EvseStatus::REMOVED !== $locationEvse->status && $locationEvse->locationWithTrashed->trashed()) {
                $locationEvse->locationWithTrashed->restore();
                LocationRestored::dispatch($location->id);
            }
            $locationEvse->locationWithTrashed->updated_at = $locationEvse->updated_at;
            $locationEvse->locationWithTrashed->save();
        }

        // Create EVSE Connectors.
        foreach ($payloadConnectorList as $payloadConnector) {
            $connectorId = $payloadConnector['id'];
            $locationConnector = new LocationConnector;
            $locationConnector->fill([
                'evse_id' => $locationEvse->id,
                'connector_id' => $connectorId,
            ]);
            unset($payloadConnector['id']);
            $locationConnector->object = $payloadConnector;
            $locationConnector->save();
            $locationConnector->refresh();
            if (true === $dispatchEvent) {
                LocationConnectorCreated::dispatch($locationConnector->id);
            }
        }

        return $locationEvse->refresh();
    }

    private function evseReplace(
        LocationEvse $locationEvse,
        array $payload,
        bool $updateLocation = true,
        bool $dispatchEvent = true
    ): bool {
        if (EvseStatus::REMOVED === EvseStatus::tryFrom($payload['status'] ?? '')) {
            return $this->eveseDelete($locationEvse);
        }

        // Replace EVSE.
        $payloadConnectorList = $payload['connectors'] ?? [];
        unset($payload['connectors']);

        // No Connector => Delete EVSE.
        if (count($payloadConnectorList ?? []) === 0) {
            return $this->eveseDelete($locationEvse);
        }

        if ($locationEvse->trashed()) {
            $locationEvse->restore();
            LocationEvseRestored::dispatch($locationEvse->id);
        }

        $locationEvse->object = $payload;
        $locationEvse->save();
        $locationEvse->refresh();
        if (true === $dispatchEvent) {
            LocationEvseReplaced::dispatch($locationEvse->id);
        }

        // Touch Location.
        if ($updateLocation) {
            if ($locationEvse->locationWithTrashed->trashed()) {
                $locationEvse->locationWithTrashed->restore();

                LocationRestored::dispatch($locationEvse->location_id);
            }
            $locationEvse->locationWithTrashed->updated_at = $payload['last_updated'] ?? Carbon::now();
            $locationEvse->locationWithTrashed->save();
        }

        $locationEvse->loadMissing('connectorsWithTrashed');

        // Create or replace EVSE Connectors.
        foreach ($payloadConnectorList as $payloadConnector) {
            $connectorId = $payloadConnector['id'];
            $lastUpdated = $payload['last_updated'] ?? Carbon::now();
            unset($payloadConnector['id'], $payloadConnector['last_updated']);
            $locationConnector = $locationEvse
                ->connectorsWithTrashed
                ->where('connector_id', $connectorId)
                ->first();
            if (null === $locationConnector) {
                $locationConnector = new LocationConnector;
                $locationConnector->fill([
                    'evse_id' => $locationEvse->id,
                    'connector_id' => $connectorId,
                ]);
                $locationConnector->updated_at = $lastUpdated;
                $locationConnector->object = $payloadConnector;
                $locationConnector->save();
                $locationConnector->refresh();
                if (true === $dispatchEvent) {
                    LocationConnectorCreated::dispatch($locationConnector->id);
                }
            } else {
                if ($locationConnector->trashed()) {
                    $locationConnector->restore();
                }
                if (true === $dispatchEvent) {
                    LocationConnectorReplaced::dispatch($locationConnector->id);
                }
            }

            if (!$this->connectorUpdate(
                $locationEvse,
                $connectorId,
                $payloadConnector
            )) {
                return false;
            }
        }
        // Delete missing EVSE Connectors.
        $payloadConnectorIdList = collect($payloadConnectorList)->pluck('id')->toArray();
        $locationEvse->connectors()->whereNotIn('connector_id', $payloadConnectorIdList)->delete();

        return true;
    }

    private function evseObjectUpdate(array $payload, LocationEvse $locationEvse): bool
    {
        // Delete EVSE.
        if (EvseStatus::REMOVED === EvseStatus::tryFrom($payload['status'] ?? '')) {
            return $this->eveseDelete($locationEvse);
        }

        foreach ($payload as $field => $value) {
            $locationEvse->object[$field] = $value;
        }

        $locationEvse->locationWithTrashed->updated_at = $payload['last_updated'] ?? Carbon::now();
        if (!$locationEvse->locationWithTrashed->save()) {
            return false;
        }

        if (!$locationEvse->save()) {
            return false;
        }

        LocationEvseUpdated::dispatch($locationEvse->id);

        if ($locationEvse->trashed()) {
            $locationEvse->restore();

            LocationEvseRestored::dispatch($locationEvse->id);

            $connectors = $locationEvse->connectorsWithTrashed()->whereNotNull('deleted_at')->get();
            foreach ($connectors as $connector) {
                $connector->restore();
            }

            if ($locationEvse->locationWithTrashed->trashed()) {
                $locationEvse->locationWithTrashed->restore();

                LocationRestored::dispatch($locationEvse->locationWithTrashed->id);
            }
        }

        return true;
    }

    private function connectorCreateOrReplace(LocationEvse $locationEvse, string $connectorId, array $payload): bool
    {
        $locationEvse->loadMissing('connectorsWithTrashed');

        $locationConnector = $locationEvse
            ->connectorsWithTrashed
            ->where('connector_id', $connectorId)
            ->first();

        $lastUpdated = $payload['last_updated'] ?? now();
        unset($payload['id'], $payload['last_updated']);

        if (null === $locationConnector) {
            $locationConnector = new LocationConnector;
            $locationConnector->fill([
                'evse_id' => $locationEvse->id,
                'connector_id' => $connectorId,
                'object' => $payload,
                'updated_at' => $lastUpdated,
            ]);
            $locationConnector->save();
            $locationConnector->refresh();

            LocationConnectorCreated::dispatch($locationConnector->id);
        }
        if ($locationConnector->trashed()) {
            $locationConnector->restore();
        }
        $this->connectorUpdate(
            $locationEvse,
            $connectorId,
            $payload
        );
        LocationConnectorReplaced::dispatch($locationConnector->id);

        $locationEvse->updated_at = $lastUpdated;
        $locationEvse->save();

        $locationEvse->locationWithTrashed->updated_at = $lastUpdated;
        $locationEvse->locationWithTrashed->save();

        return true;
    }

    private function connectorUpdate(LocationEvse $locationEvse, string $connectorId, array $payload): bool
    {
        $connector = LocationConnector::query()
            ->withTrashed()
            ->where('evse_id', $locationEvse->id)
            ->where('connector_id', $connectorId)
            ->first();
        $temp = $connector->object;
        foreach ($payload as $field => $value) {
            $temp[$field] = $value;
        }
        $connector->object = $temp;
        $connector->save();
        $connector->refresh();
        LocationConnectorUpdated::dispatch($connector->id);
        return true;
    }

    private function connectorObjectUpdate(
        array $payload,
        LocationConnector $locationConnector,
        LocationEvse $locationEvse
    ): ?LocationConnector {
        unset($payload['id']);
        $locationConnector->object = $payload;

        // Touch EVSE, Location.
        if (($payload['last_updated'] ?? null) !== null) {
            $locationEvse->updated_at = $payload['last_updated'];
            $locationEvse->save();
            $locationEvse->refresh();
            $locationEvse->locationWithTrashed->updated_at = $payload['last_updated'];
            $locationEvse->locationWithTrashed->save();
        }

        $this->connectorUpdate(
            $locationEvse,
            $locationConnector->connector_id,
            $payload
        );

        LocationConnectorUpdated::dispatch(
            $locationConnector->id
        );

        return $locationConnector->refresh();
    }

    private function connectorSearch(string $connectorId, string $evseUid): ?LocationConnector
    {
        return LocationConnector::query()
            ->withWhereHas(
                'evse',
                function ($query) use ($evseUid) {
                    $query->where('uid', $evseUid);
                }
            )
            ->where('connector_id', $connectorId)
            ->withTrashed()
            ->first();
    }

    private function eveseDelete(LocationEvse $locationEvse, bool $dispatchEvent = true): bool
    {
        $locationEvse->delete();
        $locationEvse->connectors()->delete();
        if (true === $dispatchEvent) {
            LocationEvseRemoved::dispatch($locationEvse->id);
        }

        if (0 === $locationEvse->locationWithTrashed->evses()->count()) {
            $locationEvse->locationWithTrashed->delete();
            if (true === $dispatchEvent) {
                LocationRemoved::dispatch($locationEvse->location_id);
            }
        }
        return true;
    }
}
