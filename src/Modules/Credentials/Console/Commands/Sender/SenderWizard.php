<?php

namespace Ocpi\Modules\Credentials\Console\Commands\Sender;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Credentials\Actions\Party\SelfCredentialsGetAction;
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
    ): int {
        info('OCPI Handshake Wizard — Sender flow');
        note('Register a counter-party from their Token A and complete the credentials exchange.');

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
            $this->error('Selected local party ' . $ourRole->value . ' role has no unregistered token available.');

            return Command::FAILURE;
        }

        if ($parentRole->url === null) {
            $this->error(
                'Selected local party ' . $ourRole->value . ' role has no URL configured. Set it before initiating a handshake.'
            );

            return Command::FAILURE;
        }

        $clientRole = $ourRole === Role::CPO ? Role::EMSP : Role::CPO;

        $alias = text(
            label: 'Counter-party alias (free-form name for this handshake)',
            required: true,
        );

        $version = select(
            label: 'OCPI version',
            options: ['2.2.1' => '2.2.1', '2.1.1' => '2.1.1'],
            default: '2.2.1',
        );

        $versionsUrl = text(
            label: 'Counter-party Versions endpoint URL',
            placeholder: 'https://example.com/ocpi/cpo/versions',
            required: true,
            validate: fn(string $v) => filter_var($v, FILTER_VALIDATE_URL) ? null : 'Must be a valid URL.',
        );

        $tokenA = password(
            label: 'Token A (credentials token shared by the counter-party)',
            required: true,
        );

        $mockCode = uniqid('PENDING_', true);
        $mockPartyId = 'MOC';
        $mockCountryCode = 'XX';

        note(
            sprintf(
                "Review:\n  Local party  : %s\n  Counter-party: (pending — IDs received from %s response) — %s\n  Expected role: %s\n  Versions URL : %s\n  OCPI version : %s",
                $parentParty->code,
                'credentials',
                $alias,
                $clientRole->value,
                $versionsUrl,
                $version,
            )
        );

        if (!confirm('Create this counter-party and run the credentials exchange now?', default: true)) {
            $this->warn('Aborted by user.');

            return Command::FAILURE;
        }

        $connection = config('ocpi.database.connection');

        try {
            DB::connection($connection)->beginTransaction();

            /** @var Party $party */
            $party = Party::query()->create([
                'code' => $mockCode,
                'version' => $version,
                'version_url' => $versionsUrl,
                'parent_id' => $parentParty->id,
            ]);

            /** @var PartyRole $partyRole */
            $partyRole = $party->roles()->create([
                'code' => $mockPartyId,
                'role' => $clientRole->value,
                'country_code' => $mockCountryCode,
                'parent_role_id' => $parentRole->id,
                'url' => $versionsUrl,
            ]);

            /** @var PartyToken $partyToken */
            $partyToken = $partyRole->tokens()->create([
                'token' => GeneratorHelper::decodeToken($tokenA, $version),
                'name' => $alias,
                'registered' => false,
            ]);

            DB::connection($connection)->commit();
        } catch (Exception $e) {
            DB::connection($connection)->rollBack();
            $this->error('Failed to create counter-party: ' . $e->getMessage());

            return Command::FAILURE;
        }

        info('Counter-party ' . $party->code . ' stored. Starting credentials exchange.');

        try {
            DB::connection($connection)->beginTransaction();

            spin(
                fn() => $versionsSync->handle($partyToken),
                'GET versions information & details',
            );
            $party->refresh();
            $partyToken->refresh();

            $client = new ReceiverClient($partyToken, 'credentials');
            $self = $selfCredentialsGet->handle($parentToken);
            $response = spin(
                fn() => $client->credentials()->post($self),
                'POST credentials',
            );
            $validated = CredentialsValidator::validate($response);
            $tokenB = GeneratorHelper::decodeToken($validated['token'], $party->version);
            $partyToken->token = $tokenB;
            $partyToken->registered = true;
            $partyToken->save();

            $returnedRoles = $validated['roles'];
            $returnedRoleNames = array_column($returnedRoles, 'role');

            $party->roles()
                ->whereNotIn('role', $returnedRoleNames)
                ->get()
                ->each(fn(PartyRole $r) => $r->delete());

            foreach ($returnedRoles as $i => $roleData) {
                $attributes = [
                    'code' => $roleData['party_id'],
                    'country_code' => $roleData['country_code'],
                    'business_details' => $roleData['business_details'],
                    'url' => $validated['url'],
                ];

                $existing = $party->roles()->where('role', $roleData['role'])->first();
                if ($existing === null) {
                    /** @var PartyRole $newRole */
                    $newRole = $party->roles()->create(array_merge($attributes, [
                        'role' => $roleData['role'],
                        'parent_role_id' => $parentRole->id,
                    ]));
                    /** @var PartyToken $newToken */
                    $newToken = $newRole->tokens()->create([
                        'token' => $tokenB,
                        'name' => $alias . '_' . $clientRole->value,
                        'registered' => true,
                    ]);
                    $versionsSync->handle($newToken);
                } else {
                    $existing->fill($attributes)->save();
                }

                if ($i === 0) {
                    $party->code = $roleData['country_code'] . '*' . $roleData['party_id'];
                    $party->save();
                }
            }

            $parentToken->registered = true;
            $parentToken->save();

            DB::connection($connection)->commit();
        } catch (Exception|\Throwable $e) {
            DB::connection($connection)->rollBack();
            $this->error('Credentials exchange failed: ' . $e->getMessage());
            $this->warn(
                'Counter-party ' . $party->code . ' is saved. Retry with: php artisan ocpi:sender:credentials:register ' . $party->code
            );

            return Command::FAILURE;
        }

        info('Handshake completed for ' . $party->code . '.');

        return Command::SUCCESS;
    }
}