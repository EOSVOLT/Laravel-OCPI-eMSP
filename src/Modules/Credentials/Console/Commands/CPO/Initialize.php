<?php

namespace Ocpi\Modules\Credentials\Console\Commands\CPO;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Ocpi\Models\Party;

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
        $partyId = $this->generateUniquePartyId();
        $input = [];
        $input['name'] = $this->ask('Party name');
        $input['code'] = $partyId;
        $input['url'] = config('ocpi.client.server.url').'/versions';
        $input['server_token'] = Str::random(32);
        try {
            /** @var Party $party */
            $party = Party::create($input);
        } catch (Exception $e) {
            $this->error('Error creating Party.');
            $this->newLine(2);
            $this->error($e);

            return Command::FAILURE;
        }

        $this->info('Party "'.$party->code.'" created successfully.');
        $this->info('Credentials exchange can be launch executing: php artisan ocpi:credentials:register '.$party->server_token);

        return Command::SUCCESS;
    }

    private function generateUniquePartyId(): string
    {
        do {
            $randomString = Str::random(3); // Or your desired length
        } while (Party::query()->where('code', $randomString)->exists());

        return $randomString;
    }
}
