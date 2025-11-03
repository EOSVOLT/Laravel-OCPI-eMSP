<?php

namespace Ocpi\Modules\Credentials\Console\Commands\Sender;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\DB;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Credentials\Actions\Party\SelfCredentialsGetAction;
use Ocpi\Modules\Credentials\Validators\V2_2_1\CredentialsValidator;
use Ocpi\Modules\Versions\Actions\PartyInformationAndDetailsSynchronizeAction;
use Ocpi\Support\Client\ReceiverClient;
use Ocpi\Support\Helpers\GeneratorHelper;

/**
 * @todo revisit again when we will doing as EMSP
 */
class Register extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ocpi:sender:credentials:register {party_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Credentials exchange with a "Receiver" Party';

    /**
     * Execute the console command.
     */
    public function handle(
        PartyInformationAndDetailsSynchronizeAction $versionsPartyInformationAndDetailsSynchronizeAction,
        SelfCredentialsGetAction $selfCredentialsGetAction,
    ) {
        $partyCode = $this->argument('party_code');
        $this->info('Starting credentials exchange with a sender party ' . $partyCode);

        // Retrieve the Party.
        /** @var Party $party */
        $party = Party::query()->where('code', $partyCode)->first();
        if ($party === null) {
            $this->error('Party not found.');

            return Command::FAILURE;
        }

        if ($party->tokens->first()->registered === true) {
            $this->error('Party already registered.');
            return Command::FAILURE;
        }

        try {
            DB::connection(config('ocpi.database.connection'))->beginTransaction();
            $parentParty = $party->parent;
            /** @var PartyToken $parentToken */
            $parentToken = $parentParty->tokens->first();
            // OCPI GET calls for Versions Information and Details of the Party, store OCPI endpoints.
            $this->info('  - Call Party OCPI - GET - Versions Information and Details, store OCPI endpoints');
            /** @var PartyToken $token */
            $token = $party->tokens->first();
            $party = $versionsPartyInformationAndDetailsSynchronizeAction->handle($party, $token);
            $party->save();

            DB::connection(config('ocpi.database.connection'))->commit();

            DB::connection(config('ocpi.database.connection'))->beginTransaction();

            // OCPI POST call to update the Credentials and get new Server Token.
            $this->info('  - Call Party OCPI - POST - Credentials endpoint with a parent token');
            $ocpiClient = new ReceiverClient($party, $token, 'credentials');
            $credentialsPostData = $ocpiClient->credentials()->post(
                $selfCredentialsGetAction->handle($parentParty, $parentToken)
            );
            $credentialsInput = CredentialsValidator::validate($credentialsPostData);

            // Store received OCPI Server Token, mark the Party as registered.
            $this->info(
                '  - Store received OCPI Server Token: ' . $credentialsInput['token'] . ', mark the Party as registered'
            );
            $this->info('  - Creating party roles from OCPI server');
            foreach ($credentialsInput['roles'] as $role) {
                $partyRole = new PartyRole;
                $partyRole->fill([
                    'code' => $role['party_id'],
                    'role' => $role['role'],
                    'country_code' => $role['country_code'],
                    'business_details' => $role['business_details'],
                ]);
                $party->roles()->save($partyRole);
            }
            $parentToken->registered = true;
            $parentToken->save();
            $token->token = GeneratorHelper::decodeToken($credentialsInput['token'], $party->version);
            $token->registered = true;
            $token->save();

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
