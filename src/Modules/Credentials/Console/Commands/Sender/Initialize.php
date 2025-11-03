<?php

namespace Ocpi\Modules\Credentials\Console\Commands\Sender;

use Exception;
use Illuminate\Console\Command;
use Ocpi\Models\Party;
use Ocpi\Models\PartyToken;

/**
 * @todo revisit again when doing a EMSP role.
 */
class Initialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ocpi:sender:credentials:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize a new "Sender" Party to start credentials exchange';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $input = [];
        $parentPartyId = $this->ask('Parent Party Increment ID(our CPO party)');
        if(false === Party::query()->where('id', $parentPartyId)->exists()) {
            $this->error('Parent Party is missing, please create our party first.');
            return Command::FAILURE;
        }

        $name = $this->ask('Party alies name');
        $input['code'] = $this->ask('Party ID or code');
        $input['parent_id'] = $parentPartyId;
        if (Party::query()->where('code', $input['code'])->exists()) {
            $this->error('Party already exists.');
            return Command::FAILURE;
        }
        $input['url'] = $this->ask('URL of API versions endpoint');
        $token = $this->ask('Credentials Token');
        try {
            /** @var Party $party */
            $party = Party::query()->create($input);
            $partyToken = new PartyToken();
            $partyToken->fill([
                'token' => $token,
                'registered' => false,
                'name' => $name . '_' . $input['code'],
            ]);
            $party->tokens()->save($partyToken);
        } catch (Exception $e) {
            $this->error('Error creating Party.');
            $this->newLine(2);
            $this->error($e);

            return Command::FAILURE;
        }

        $this->info('Party "' . $party->code . '" created successfully.');
        $this->info(
            'Credentials exchange can be launch executing: php artisan ocpi:sender:credentials:register ' . $party->code
        );

        return Command::SUCCESS;
    }
}
