<?php

namespace Ocpi\Modules\Credentials\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\DB;
use Ocpi\Models\Party;
use Ocpi\Modules\Credentials\Actions\Party\SelfCredentialsGetAction;
use Ocpi\Modules\Credentials\Validators\V2_1_1\CredentialsValidator;
use Ocpi\Modules\Versions\Actions\PartyInformationAndDetailsSynchronizeAction as VersionsPartyInformationAndDetailsSynchronizeAction;
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
    protected $description = 'Credentials exchange with a new "Receiver" Party';

    /**
     * Execute the console command.
     */
    public function handle(
        VersionsPartyInformationAndDetailsSynchronizeAction $versionsPartyInformationAndDetailsSynchronizeAction,
        SelfCredentialsGetAction $selfCredentialsGetAction,
    ) {
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

        try {
            DB::connection(config('ocpi.database.connection'))->beginTransaction();

            // OCPI GET calls for Versions Information and Details of the Party, store OCPI endpoints.
            $this->info('  - Call Party OCPI - GET - Versions Information and Details, store OCPI endpoints');
            $party = $versionsPartyInformationAndDetailsSynchronizeAction->handle($party);

            // Generate new Client Token for the Party.
            $party->client_token = $party->generateToken();
            $this->info('  - Generate, store new OCPI Client Token: '.$party->client_token);
            $party->save();

            DB::connection(config('ocpi.database.connection'))->commit();

            DB::connection(config('ocpi.database.connection'))->beginTransaction();

            // OCPI POST call to update the Credentials and get new Server Token.
            $this->info('  - Call Party OCPI - POST - Credentials endpoint with new Client Token');
            $ocpiClient = new Client($party, 'credentials');
            $credentialsPostData = $ocpiClient->credentials()->post($selfCredentialsGetAction->handle($party));
            $credentialsInput = CredentialsValidator::validate($credentialsPostData);

            // Store received OCPI Server Token, mark the Party as registered.
            $this->info('  - Store received OCPI Server Token: '.$credentialsInput['token'].', mark the Party as registered');
            $party->server_token = Party::decodeToken($credentialsInput['token'], $party);
            $party->registered = true;
            $party->save();

            DB::connection(config('ocpi.database.connection'))->commit();

            return Command::SUCCESS;
        } catch (Exception $e) {
            DB::connection(config('ocpi.database.connection'))->rollback();

            $this->error($e->getMessage());

            return Command::FAILURE;
        }

    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'party_code' => 'Which Party should be registered?',
        ];
    }
}
