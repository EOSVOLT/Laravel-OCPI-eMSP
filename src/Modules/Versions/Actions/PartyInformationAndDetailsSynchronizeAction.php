<?php

namespace Ocpi\Modules\Versions\Actions;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Ocpi\Models\Party;
use Ocpi\Models\PartyToken;
use Ocpi\Support\Client\Client;
use Ocpi\Support\Enums\Role;

class PartyInformationAndDetailsSynchronizeAction
{
    public function handle(Party $party, PartyToken $partyToken, Role $role): Party
    {
        // OCPI GET call for Versions Information of the Party, store OCPI version and URL.
        Log::channel('ocpi')->info('Party '.$party->code.' - OCPI GET call for Versions Information of the Party on '.$party->roles->where('role', $role->value)->first()->url);
        $client = new Client($party, $partyToken, 'versions.information', $role);
        $versionList = $client->versions()->information();
        throw_if(
            ! is_array($versionList),
            new Exception('Party '.$party->code.' - Empty or invalid response for Versions Information.')
        );

        // Find supported OCPI versions.
        $supportedVersionList = array_keys((config('ocpi-cpo.versions', [])));
        throw_if(
            count($supportedVersionList) === 0,
            new Exception('No supported version found.')
        );

        // Find party OCPI versions.
        $partyVersionList = Arr::sortDesc(
            Arr::keyBy(
                Arr::where($versionList, function ($version) {
                    return Arr::has($version, ['version', 'url']);
                }),
                'version')
        );
        throw_if(
            count($partyVersionList) === 0,
            new Exception('Party '.$party->code.' - No valid version found for Party.')
        );

        // Find latest mutual OCPI version.
        $latestMutualVersion = null;
        foreach ($partyVersionList as $version => $item) {
            if (in_array($version, $supportedVersionList)) {
                $latestMutualVersion = $item;
                break;
            }
        }
        throw_if(
            $latestMutualVersion === null,
            new Exception('Party '.$party->code.' - No mutual version found.')
        );

        Log::channel('ocpi')->info('Party '.$party->code.' - Set Party OCPI version to '.$latestMutualVersion['version']);
        $party->version = $latestMutualVersion['version'];
        $party->version_url = $latestMutualVersion['url'];
        throw_if(
            ! $party->save(),
            new Exception('Party '.$party->code.' - Error updating Party OCPI version.')
        );

        // OCPI GET call for Versions Details of the Party, store OCPI endpoints.
        Log::channel('ocpi')->info('Party '.$party->code.' - OCPI GET call for Versions Details of the Party for version '.$party->version);
        $client->module('versions.details');
        $versionDetails = $client->versions()->details();
        throw_if(
            ! is_array($versionDetails) || ! isset($versionDetails['version']) || ! is_array($versionDetails['endpoints'] ?? null),
            new Exception('Party '.$party->code.' - Empty or invalid response for Versions Details.')
        );
        throw_if(
            $versionDetails['version'] !== $party->version,
            new Exception('Party '.$party->code.' - Version mismatch for Versions Details: requested '.$party->version.' / received '.$versionDetails['version'].'.')
        );

        // Set Party OCPI endpoints for version.
        Log::channel('ocpi')->info('Party '.$party->code.' - Set OCPI endpoints for version '.$party->version);
        $endpoints = [];
        foreach ($versionDetails['endpoints'] as $endpoint) {
            $key = $endpoint['identifier'];
            $innerKey = $endpoint['role'];
            $endpoints[$key][$innerKey] = rtrim($endpoint['url'], '/');
        }
        $party->endpoints = $endpoints;
        throw_if(
            ! Arr::has($party->endpoints, 'credentials'),
            new Exception('Party '.$party->code.' - Missing required `credentials` Module endpoint.')
        );

        throw_if(
            ! $party->save(),
            new Exception('Party '.$party->code.' - Error updating Party OCPI endpoints.')
        );

        return $party;
    }
}
