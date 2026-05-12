<?php

namespace Ocpi\Modules\Credentials\Console\Commands\Sender;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Credentials\Actions\Party\SelfCredentialsGetAction;
use Ocpi\Modules\Credentials\Actions\PartyRole\SyncPartyRoleAction;
use Ocpi\Modules\Credentials\Console\Commands\CredentialCommandTrait;
use Ocpi\Modules\Credentials\Validators\V2_2_1\CredentialsValidator;
use Ocpi\Modules\Versions\Actions\PartyInformationAndDetailsSynchronizeAction;
use Ocpi\Support\Client\ReceiverClient;
use Ocpi\Support\Enums\Role;
use Ocpi\Support\Helpers\GeneratorHelper;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;
use function Ocpi\Modules\Credentials\Console\Commands\config;

class SenderWizard extends Command
{
    use CredentialCommandTrait;
    protected $signature = 'ocpi:credentials:wizard:sender';

    protected $description = 'Interactive wizard to perform an OCPI Sender credentials handshake end-to-end';

    public function handle(
        PartyInformationAndDetailsSynchronizeAction $versionsSync,
        SelfCredentialsGetAction $selfCredentialsGet,
        SyncPartyRoleAction $syncPartyRoleAction,
    ): int {
        info('OCPI Handshake Wizard — Sender flow');
        note('Register a Mocked-party from their Token A and complete the credentials exchange.');

        $parentChoice = select(
            label: 'Parent party (your local OCPI identity for this handshake)',
            options: [
                'existing' => 'Use an existing party',
                'new' => 'Create a new party',
            ],
            default: 'existing',
        );

        if ($parentChoice === 'new') {
            $localCountryCode = strtoupper(text(
                label: 'Local country code (ISO 3166-1 alpha-2)',
                placeholder: 'TH',
                required: true,
                validate: fn(string $v) => strlen(trim($v)) === 2 ? null : 'Country code must be 2 characters.',
            ));
            $localVersion = select(
                label: 'Local OCPI version',
                options: ['2.2.1' => '2.2.1', '2.1.1' => '2.1.1'],
                default: '2.2.1',
            );
            $businessName = text(label: 'Local company name', required: true);
            $businessWebsite = text(
                label: 'Local company website',
                required: true,
                validate: fn(string $v) => filter_var($v, FILTER_VALIDATE_URL) ? null : 'Must be a valid URL.',
            );

            $connection = config('ocpi.database.connection');
            try {
                DB::connection($connection)->beginTransaction();

                $partyCode = GeneratorHelper::generateUniquePartyCode($localCountryCode);
                $businessDetails = ['name' => $businessName, 'website' => $businessWebsite];

                /** @var Party $parentParty */
                $parentParty = Party::query()->create([
                    'code' => $partyCode->getCodeFormatted(),
                    'version' => $localVersion,
                ]);
                $parentCpoRole = $this->createPartyRole($parentParty, Role::CPO, $localCountryCode, $businessDetails);
                $parentEmsRole = $this->createPartyRole($parentParty, Role::EMSP, $localCountryCode, $businessDetails);
                $parentParty->load('roles.tokens');

                DB::connection($connection)->commit();
            } catch (Exception $e) {
                DB::connection($connection)->rollBack();
                $this->error('Failed to create parent party: ' . $e->getMessage());

                return Command::FAILURE;
            }
            info('Local parent party "' . $parentParty->code . '" created with CPO and eMSP roles.');
            info('Parent party ID: ' . $parentParty->id);
            info('Parent CPO role id: ' . $parentCpoRole->id);
            info('Parent eMSP role id: ' . $parentEmsRole->id);
        } else {
            $parentPartyId = (int)text(
                label: 'Local parent party ID (numeric) initiating the handshake',
                required: true,
                validate: fn(string $v) => ctype_digit(trim($v)) ? null : 'Must be a numeric party ID.',
            );

            /** @var Party|null $parentParty */
            $parentParty = Party::query()
                ->with('roles.tokens')
                ->find($parentPartyId);
            if ($parentParty === null) {
                $this->error('Parent party with ID ' . $parentPartyId . ' not found.');

                return Command::FAILURE;
            }
        }

        $ourRole = Role::from(
            select(
                label: 'Initiate handshake as(Our ROLE)?',
                options: [
                    Role::CPO->value => 'CPO',
                    Role::EMSP->value => 'eMSP',
                ],
                default: Role::CPO->value,
            )
        );

        /** @var PartyRole|null $parentRole */
        $parentRole = $parentParty->roles->firstWhere('role', $ourRole->value);
        if ($parentRole === null) {
            $this->error(
                'Selected local party has no ' . $ourRole->value . ' role configured. Configure that role + token first.'
            );

            return Command::FAILURE;
        }

        /** @var PartyToken|null $parentToken */
        $parentToken = $parentRole->tokens()->where('registered', false)->first();
        if ($parentToken === null) {
            if (!confirm(
                label: 'No unregistered token found on ' . $ourRole->value . ' role. Generate a new one?',
                default: true,
            )) {
                $this->warn('Aborted by user.');

                return Command::FAILURE;
            }

            $newTokenName = text(
                label: 'Token label',
                default: 'wizard',
                required: true,
            );

            /** @var PartyToken $parentToken */
            $parentToken = $parentRole->tokens()->create([
                'token' => GeneratorHelper::generateToken($parentParty->code),
                'name' => $newTokenName,
                'registered' => false,
            ]);

            info('Generated new token "' . $parentToken->token . '" on ' . $ourRole->value . ' role.');
        }

        if ($parentRole->url === null) {
            $this->error(
                'Selected local party ' . $ourRole->value . ' role has no URL configured. Set it before initiating a handshake.'
            );

            return Command::FAILURE;
        }

        $clientRole = $ourRole === Role::CPO ? Role::EMSP : Role::CPO;

        $alias = 'Mocked-party alias';

        $version = select(
            label: 'OCPI version',
            options: ['2.2.1' => '2.2.1', '2.1.1' => '2.1.1'],
            default: '2.2.1',
        );

        $versionsUrl = text(
            label: 'Client party Versions endpoint URL',
            placeholder: 'https://example.com/ocpi/cpo/versions',
            required: true,
            validate: fn(string $v) => filter_var($v, FILTER_VALIDATE_URL) ? null : 'Must be a valid URL.',
        );

        $tokenA = password(
            label: 'Token A (credentials token shared by the Client party)',
            required: true,
        );

        $mockCode = 'TEMP_' . Str::upper(Str::random(6));
        $mockPartyId = 'MOC';
        $mockCountryCode = 'XX';

        note(
            sprintf(
                "Review:\n  Local party  : %s\n  Mocked-party: (pending — IDs received from %s response) — %s\n  Expected role: %s\n  Versions URL : %s\n  OCPI version : %s",
                $parentParty->code,
                'credentials',
                $alias,
                $clientRole->value,
                $versionsUrl,
                $version,
            )
        );

        if (!confirm('Create this Mocked-party and run the credentials exchange now?', default: true)) {
            $this->warn('Aborted by user.');

            return Command::FAILURE;
        }

        $connection = config('ocpi.database.connection');

        try {
            DB::connection($connection)->beginTransaction();

            /** @var Party $mockedParty */
            $mockedParty = Party::query()->create([
                'code' => $mockCode,
                'version' => $version,
                'version_url' => $versionsUrl,
                'parent_id' => $parentParty->id,
            ]);

            /** @var PartyRole $mockedPartyRole */
            $mockedPartyRole = $mockedParty->roles()->create([
                'code' => $mockPartyId,
                'role' => $clientRole->value,
                'country_code' => $mockCountryCode,
                'parent_role_id' => $parentRole->id,
                'url' => $versionsUrl,
            ]);

            /** @var PartyToken $mockedPartyToken */
            $mockedPartyToken = $mockedPartyRole->tokens()->create([
                'token' => GeneratorHelper::decodeToken($tokenA, $version),
                'name' => $alias,
                'registered' => false,
            ]);

            DB::connection($connection)->commit();
        } catch (Exception $e) {
            DB::connection($connection)->rollBack();
            $this->error('Failed to create Mocked-party: ' . $e->getMessage());

            return Command::FAILURE;
        }

        info('Mocked-party ' . $mockedParty->code . ' stored. Starting credentials exchange.');

        try {
            DB::connection($connection)->beginTransaction();

            spin(
                fn() => $versionsSync->handle($mockedPartyToken),
                'GET versions information & details',
            );
            $mockedParty->refresh();
            $mockedPartyToken->refresh();

            $client = new ReceiverClient($mockedPartyToken, 'credentials');
            $self = $selfCredentialsGet->handle($parentToken);
            $response = spin(
                fn() => $client->credentials()->post($self),
                'POST credentials',
            );
            $validated = CredentialsValidator::validate($response);
            //create children's parties and roles according to the response
            $syncPartyRoleAction->handle($parentToken, $validated);
            $parentToken->registered = true;
            $parentToken->save();

            //rename code to free the unique slot, then soft-delete (cascades to its role and token).
            $mockedParty->code = $mockedParty->code . '_DELETED_' . now()->timestamp;
            $mockedParty->save();
            $mockedParty->delete();

            DB::connection($connection)->commit();
        } catch (Exception|\Throwable $e) {
            DB::connection($connection)->rollBack();
            $this->error('Credentials exchange failed: ' . $e->getMessage());
            $this->warn(
                'Mocked-party ' . $mockedParty->code . ' is saved. Retry with: php artisan ocpi:sender:credentials:register ' . $mockedParty->code
            );

            return Command::FAILURE;
        }

        info('Handshake completed. Mocked-party removed; real children registered from credentials response.');

        return Command::SUCCESS;
    }
}