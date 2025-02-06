<?php

namespace Ocpi\Modules\Versions\Actions;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Ocpi\Models\Party;
use Ocpi\Support\Client\Client as OcpiClient;

class PartyInformationAndDetailsSynchronizeAction
{
    public function handle(Party $party): Party
    {
        // OCPI GET call for Versions Information of the Party, store OCPI version and URL.
        Log::channel('ocpi')->info('Party '.$party->code.' - OCPI GET call for Versions Information of the Party on '.$party->url);
        $ocpiClient = new OcpiClient($party, 'versions.information');
        $versionList = $ocpiClient->versions()->information();
        throw_if(
            ! is_array($versionList),
            new Exception('Party '.$party->code.' - Empty or invalid response for Versions Information.')
        );

        // Find most recent version.
        $currentItem = null;
        foreach ($versionList as $item) {
            if ($currentItem === null || version_compare(($item['version'] ?? null), ($currentItem['version'] ?? null), '>')) {
                $currentItem = $item;
            }
        }
        throw_if(
            $currentItem === null || ! Arr::has($currentItem, ['version', 'url']),
            new Exception('Party '.$party->code.' - No version found.')
        );

        Log::channel('ocpi')->info('Party '.$party->code.' - Set Party OCPI version to '.$currentItem['version']);
        $party->version = $currentItem['version'];
        $party->version_url = $currentItem['url'];
        throw_if(
            ! $party->save(),
            new Exception('Party '.$party->code.' - Error updating Party OCPI version.')
        );

        // OCPI GET call for Versions Details of the Party, store OCPI endpoints.
        Log::channel('ocpi')->info('Party '.$party->code.' - OCPI GET call for Versions Details of the Party for version '.$party->version);
        $ocpiClient->module('versions.details');
        $versionDetails = $ocpiClient->versions()->details();
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
        $party->endpoints = collect($versionDetails['endpoints'])
            ->pluck('url', 'identifier')
            ->toArray();
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
