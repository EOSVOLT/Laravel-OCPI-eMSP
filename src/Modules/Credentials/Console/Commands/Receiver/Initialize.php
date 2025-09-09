<?php

namespace Ocpi\Modules\Credentials\Console\Commands\Receiver;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Support\Enums\Role;
use Ocpi\Support\Helpers\GeneratorHelper;

class Initialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ocpi:cpo:credentials:initialize';

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
        $input['name'] = $this->ask('Party name');
        if (Party::where('name', $input['name'])->exists()) {
            $this->error('Party name already exists.');

            return Command::FAILURE;
        }
        $countryCode = $this->ask('Country code');
        $input['version'] = $this->ask('OCPI version');
        $businessName = $this->ask('Company Name');
        $businessWebsite = $this->ask('Company Website');

        $partyCode = GeneratorHelper::generateUniquePartyCode($countryCode);
        $input['code'] = $partyCode->getCodeFormatted();
        $input['url'] = config('ocpi.client.server.url') . '/cpo/versions';
        $input['server_token'] = Str::random(32);
        try {
            /** @var Party $party */
            $party = Party::query()->create($input);
            $partyRole = new PartyRole();
            $partyRole->fill([
                'code' => $partyCode->getCode(),
                'role' => Role::CPO,
                'country_code' => $partyCode->getCountryCode(),
                'business_details' => ['name' => $businessName, 'website' => $businessWebsite],
            ]);
            $party->roles()->save($partyRole);
        } catch (Exception $e) {
            $this->error('Error creating Party.');
            $this->newLine(2);
            $this->error($e);

            return Command::FAILURE;
        }

        $this->info('Party "' . $party->code . '" created successfully.');
        $this->info('Token A "' . $party->server_token . '" created successfully.');
        $this->info('URL "' . $party->url . '" created successfully.');
        $this->info(
            'Credentials has been created, please share it to your 3rd party system and let them initiate the handshake.'
        );

        return Command::SUCCESS;
    }
}
