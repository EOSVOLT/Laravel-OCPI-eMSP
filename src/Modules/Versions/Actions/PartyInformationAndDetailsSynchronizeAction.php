<?php

namespace Ocpi\Modules\Versions\Actions;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Ocpi\Models\Party;
use Ocpi\Models\PartyToken;
use Ocpi\Support\Client\Client;

class PartyInformationAndDetailsSynchronizeAction
{
    /**
     * @param  PartyToken  $partyToken
     *
     * @return Party
     * @throws \Throwable
     */
    public function handle(PartyToken $partyToken): Party
    {
        $partyRole = $partyToken->party_role;
        $partyCode = $partyRole->code;
        $partyVersion = $partyRole->party->version;
        // OCPI GET call for Versions Information of the Party, store OCPI version and URL.
        Log::channel('ocpi')->info(
            'Party '.$partyCode.' - OCPI GET call for Versions Information of the Party on '.$partyRole->url
        );
        $client = new Client($partyToken, 'versions.information');
        $versionList = $client->versions()->information();
        throw_if(
            !is_array($versionList),
            new Exception('Party '.$partyCode.' - Empty or invalid response for Versions Information.')
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
                'version'
            )
        );
        throw_if(
            count($partyVersionList) === 0,
            new Exception('Party '.$partyCode.' - No valid version found for Party.')
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
            new Exception('Party '.$partyCode.' - No mutual version found.')
        );

        Log::channel('ocpi')->info('Party '.$partyCode.' - Set Party OCPI version to '.$latestMutualVersion['version']);
        $partyRole->party->version = $latestMutualVersion['version'];
        $partyRole->party->version_url = $latestMutualVersion['url'];
        throw_if(
            !$partyRole->party->save(),
            new Exception('Party '.$partyCode.' - Error updating Party OCPI version.')
        );

        // OCPI GET call for Versions Details of the Party, store OCPI endpoints.
        Log::channel('ocpi')->info(
            'Party '.$partyCode.' - OCPI GET call for Versions Details of the Party for version '.$partyVersion
        );
        $client->module('versions.details');
        $versionDetails = $client->versions()->details();
        throw_if(
            !is_array($versionDetails) || !isset($versionDetails['version']) || !is_array(
                $versionDetails['endpoints'] ?? null
            ),
            new Exception('Party '.$partyCode.' - Empty or invalid response for Versions Details.')
        );
        throw_if(
            $versionDetails['version'] !== $partyVersion,
            new Exception(
                'Party '.$partyCode.' - Version mismatch for Versions Details: requested '.$partyVersion.' / received '.$versionDetails['version'].'.'
            )
        );

        // Set Party OCPI endpoints for version.
        Log::channel('ocpi')->info('Party '.$partyCode.' - Set OCPI endpoints for version '.$partyVersion);
        $endpoints = [];
        foreach ($versionDetails['endpoints'] as $endpoint) {
            $key = $endpoint['identifier'];
            $innerKey = $endpoint['role'];
            $endpoints[$key][$innerKey] = rtrim($endpoint['url'], '/');
        }
        $partyRole->endpoints = $endpoints;
        throw_if(
            !Arr::has($partyRole->endpoints, 'credentials'),
            new Exception('Party '.$partyCode.' - Missing required `credentials` Module endpoint.')
        );

        throw_if(
            !$partyRole->save(),
            new Exception('Party '.$partyCode.' - Error updating Party OCPI endpoints.')
        );

        return $partyRole->party;
    }
}
