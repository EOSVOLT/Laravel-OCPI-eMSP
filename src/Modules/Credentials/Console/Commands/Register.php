<?php

namespace Ocpi\Modules\Credentials\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Arr;
use Ocpi\Models\Party;
use Ocpi\Modules\Credentials\Actions\Party\SelfCredentialsGetAction;
use Ocpi\Support\Client\Client;

class Register extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ocpi:credentials:register {party_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Credentials exchange with a new "Sender" Party';

    /**
     * Execute the console command.
     */
    public function handle(SelfCredentialsGetAction $selfCredentialsGetAction)
    {
        $partyCode = $this->argument('party_code');
        $this->info('Starting credentials exchange with '.$partyCode);

        // Retrieve the Party.
        $party = Party::where('code', $partyCode)->first();
        if ($party === null) {
            $this->error('Party not found.');

            return Command::FAILURE;
        }

        if ($party->registered === true) {
            $this->error('Party already registered.');

            return Command::FAILURE;
        }

        // OCPI GET call for Versions Information of the Party, store OCPI version and URL.
        $this->info('  - Call Party OCPI - GET - '.$party->url);
        $ocpiClient = new Client($party, 'versions.information');

        $versionList = $ocpiClient->versions()->information();
        if (! is_array($versionList)) {
            $this->error('Empty or invalid response for Versions Information.');

            return Command::FAILURE;
        }

        $currentItem = null;
        foreach ($versionList as $item) {
            if ($currentItem === null || version_compare($item->version, $currentItem->version, '>')) {
                $currentItem = $item;
            }
        }

        if ($currentItem === null || $item?->version === null || $item?->url === null) {
            $this->error('No version found.');

            return Command::FAILURE;
        }

        $this->info('  - Set Party OCPI version to '.$currentItem->version);
        $party->version = $currentItem->version;
        $party->version_url = $currentItem->url;
        if (! $party->save()) {
            $this->error('Error updating Party OCPI version.');

            return Command::FAILURE;
        }

        // OCPI GET call for Versions Details of  , store OCPI endpoints.
        $this->info('  - Call Party OCPI - GET - Versions Details endpoint for version '.$party->version);
        $ocpiClient->module('versions.details');

        $versionDetails = $ocpiClient->versions()->details();
        if (! is_array($versionDetails) || ! isset($versionDetails['version']) || ! is_array($versionDetails['endpoints'] ?? null)) {
            $this->error('Empty or invalid response for Versions Details.');

            return Command::FAILURE;
        }

        if ($versionDetails['version'] !== $party->version) {
            $this->error('Version mismatch for Versions Details: requested '.$party->version.' / received '.$versionDetails['version'].'.');

            return Command::FAILURE;
        }

        $this->info('  - Set Party OCPI endpoints for version '.$party->version);
        $party->endpoints = collect($versionDetails['endpoints'])
            ->pluck('url', 'identifier')
            ->toArray();

        if (! Arr::has($party->endpoints, 'credentials')) {
            $this->error('Missing required `credentials` Module endpoint.');

            return Command::FAILURE;
        }

        if (! $party->save()) {
            $this->error('Error updating Party OCPI endpoints.');

            return Command::FAILURE;
        }

        // Generate new Client Token for the Party.
        $party->client_token = $party->generateToken();
        $this->info('  - Store new Client Token for the Party OCPI: '.$party->client_token);
        if (! $party->save()) {
            $this->error('Error updating Party Client Token.');

            return Command::FAILURE;
        }

        // OCPI POST call to update the Credentials.
        $this->info('  - Call Party OCPI - POST - Credentials endpoint');
        $ocpiClient->module('credentials');

        try {
            $ocpiClient->credentials()->post($selfCredentialsGetAction->handle($party));
        } catch (Exception $e) {
            $this->error('Error posting Credentials to Party');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'party_code' => 'Which Party should be registered?',
        ];
    }
}
