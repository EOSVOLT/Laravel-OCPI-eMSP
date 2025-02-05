<?php

namespace Ocpi\Modules\Versions\Actions;

use Exception;
use Illuminate\Support\Arr;
use Ocpi\Models\Party;
use Ocpi\Support\Client\Client;

class PartyInformationAndDetailsSynchronizeAction
{
    public function handle(Party $party): Party
    {
        // OCPI GET call for Versions Information of the Party, store OCPI version and URL.
        $ocpiClient = new Client($party, 'versions.information');

        $versionList = $ocpiClient->versions()->information();
        throw_if(
            ! is_array($versionList),
            new Exception('Empty or invalid response for Versions Information.')
        );

        // Find most recent version.
        $currentItem = null;
        foreach ($versionList as $item) {
            if ($currentItem === null || version_compare($item->version, $currentItem->version, '>')) {
                $currentItem = $item;
            }
        }
        throw_if(
            $currentItem === null || $item?->version === null || $item?->url === null,
            new Exception('No version found.')
        );

        $party->version = $currentItem->version;
        $party->version_url = $currentItem->url;
        throw_if(
            ! $party->save(),
            new Exception('Error updating Party OCPI version.')
        );

        // OCPI GET call for Versions Details of the Party, store OCPI endpoints.
        $ocpiClient->module('versions.details');
        $versionDetails = $ocpiClient->versions()->details();
        throw_if(
            ! is_array($versionDetails) || ! isset($versionDetails['version']) || ! is_array($versionDetails['endpoints'] ?? null),
            new Exception('Empty or invalid response for Versions Details.')
        );
        throw_if(
            $versionDetails['version'] !== $party->version,
            new Exception('Version mismatch for Versions Details: requested '.$party->version.' / received '.$versionDetails['version'].'.')
        );

        // Set Party OCPI endpoints for version.
        $party->endpoints = collect($versionDetails['endpoints'])
            ->pluck('url', 'identifier')
            ->toArray();
        throw_if(
            ! Arr::has($party->endpoints, 'credentials'),
            new Exception('Missing required `credentials` Module endpoint.')
        );

        throw_if(
            ! $party->save(),
            new Exception('Error updating Party OCPI endpoints.')
        );

        return $party;
    }
}
