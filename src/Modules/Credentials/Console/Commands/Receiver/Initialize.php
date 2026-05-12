<?php

namespace Ocpi\Modules\Credentials\Console\Commands\Receiver;

use Exception;
use Illuminate\Console\Command;
use Ocpi\Models\Party;
use Ocpi\Modules\Credentials\Console\Commands\CredentialCommandTrait;
use Ocpi\Support\Enums\Role;
use Ocpi\Support\Helpers\GeneratorHelper;

class Initialize extends Command
{
    use CredentialCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ocpi:receiver:credentials:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize a new "Receiver" Party to start credentials exchange';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $input = [];
        $countryCode = $this->ask('Country code');
        $input['version'] = $this->ask('OCPI version');
        $businessName = $this->ask('Company Name');
        $businessWebsite = $this->ask('Company Website');

        $partyCode = GeneratorHelper::generateUniquePartyCode($countryCode);
        $input['code'] = $partyCode->getCodeFormatted();
        try {
            /** @var Party $party */
            $party = Party::query()->create($input);
            $cpo = $this->createPartyRole(
                $party,
                Role::CPO,
                $partyCode->getCountryCode(),
                ['name' => $businessName, 'website' => $businessWebsite]
            );
            $emsp = $this->createPartyRole(
                $party,
                Role::EMSP,
                $partyCode->getCountryCode(),
                ['name' => $businessName, 'website' => $businessWebsite]
            );
        } catch (Exception $e) {
            $this->error('Error creating Party.');
            $this->newLine(2);
            $this->error($e);

            return Command::FAILURE;
        }

        $this->info('Party "' . $party->code . '" created successfully.');
        $this->info('Role CPO "' . $cpo->tokens->first()->token . '" created successfully.');
        $this->info('CPO URL "' . $cpo->url . '" created successfully.');
        $this->info('Role EMSP "' . $emsp->tokens->first()->token . '" created successfully.');
        $this->info('EMSP URL "' . $emsp->url . '" created successfully.');
        $this->info(
            'Credentials has been created, please share it to your 3rd party system and let them initiate the handshake.'
        );

        return Command::SUCCESS;
    }
}
