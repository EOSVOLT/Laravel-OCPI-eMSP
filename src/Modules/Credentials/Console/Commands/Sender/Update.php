<?php

namespace Ocpi\Modules\Credentials\Console\Commands\Sender;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\DB;
use Ocpi\Models\Party;
use Ocpi\Modules\Credentials\Actions\Party\SelfCredentialsGetAction;
use Ocpi\Modules\Credentials\Validators\V2_2_1\CredentialsValidator;
use Ocpi\Modules\Versions\Actions\PartyInformationAndDetailsSynchronizeAction;
use Ocpi\Support\Client\Client;

class Update extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ocpi:emsp:credentials:update {party_code} {--without_new_client_token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Credentials update with a "Receiver" Party';

    /**
     * Execute the console command.
     */
    public function handle(
        PartyInformationAndDetailsSynchronizeAction $versionsPartyInformationAndDetailsSynchronizeAction,
        SelfCredentialsGetAction $selfCredentialsGetAction,
    ) {
        $partyCode = $this->argument('party_code');
        $generateNewClientToken = ! ($this->option('without_new_client_token') ?? false);
        $this->info('Starting credentials update with '.$partyCode.($generateNewClientToken ? ' with' : ' without').' new OCPI Client Token');

        // Retrieve the Party.
        $party = Party::where('code', $partyCode)->withWhereHas('role_cpo')->first();
        if ($party === null) {
            $this->error('Party not found.');

            return Command::FAILURE;
        }

        if ($party->registered === false) {
            $this->error('Party not registered.');

            return Command::FAILURE;
        }
        $partyToken = $party->tokens->first();
        try {
            DB::connection(config('ocpi.database.connection'))->beginTransaction();

            // OCPI GET calls for Versions Information and Details of the Party, store OCPI endpoints.
            $this->info('  - Call Party OCPI - GET - Versions Information and Details, store OCPI endpoints');
            $party = $versionsPartyInformationAndDetailsSynchronizeAction->handle($partyToken);

            // Generate new Client Token for the Party.
            if ($generateNewClientToken) {
//                $party->client_token = $party->generateToken();
                $this->info('  - Generate, store new OCPI Client Token: '.$party->client_token);
                $party->save();
            }

            DB::connection(config('ocpi.database.connection'))->commit();

            DB::connection(config('ocpi.database.connection'))->beginTransaction();

            // OCPI PUT call to update the Credentials and get new Server Token.
            $this->info('  - Call Party OCPI - PUT - Credentials endpoint with new Client Token');
            $ocpiClient = new Client($partyToken, 'credentials');
            $credentialsPutData = $ocpiClient->credentials()->put($selfCredentialsGetAction->handle($partyToken));
            $credentialsInput = CredentialsValidator::validate($credentialsPutData);

            // Store received OCPI Server Token.
            $this->info('  - Store received OCPI Server Token: '.$credentialsInput['token']);
//            $party->server_token = Party::decodeToken($credentialsInput['token'], $party);
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
