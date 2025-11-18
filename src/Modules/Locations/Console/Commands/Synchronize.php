<?php

namespace Ocpi\Modules\Locations\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Locations\Client\V2_2_1\CPOClient;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Enums\Role;

class Synchronize extends Command
{
    use HandlesLocation;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ocpi:locations:synchronize {--P|party=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize locations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting OCPI Locations synchronization');

        $optionParty = $this->option('party');

        $partyRoles = PartyRole::query()
            ->withWhereHas('party', function ($query) use ($optionParty) {
                $query->when($optionParty, function ($query) use ($optionParty) {
                    $query->whereIn('code', explode(',', $optionParty));
                });
                $query->where('is_external_party', true);
                $query->whereHas('parent.roles', function ($query) {
                    $query->where('role', Role::EMSP);
                });
            })->withWhereHas('tokens', function ($query) {
                $query->where('registered', true);
            })
            ->where('role', Role::CPO)
            ->get();

        if (0 === $partyRoles->count()) {
            $this->error('No Party to process.');

            return Command::FAILURE;
        }

        $hasError = false;

        foreach ($partyRoles as $role) {
            $party = $role->party;
            $this->info('  - Processing Party ' . $party->code);

            $ocpiClient = new CPOClient($role->tokens->first());

            if (empty($ocpiClient->resolveBaseUrl())) {
                $this->warn('Party ' . $party->code . ' is not configured to use the Locations module.');

                continue;
            }

            foreach ($party->roles as $partyRole) {
                $this->info(
                    '    - Call ' . $partyRole->code . ' / ' . $partyRole->country_code . ' - OCPI - Locations GET'
                );
                $offset = 0;
                $limit = 100;
                do {
                    $ocpiLocationList = $ocpiClient->locations()->get(offset: $offset, limit: $limit)?->getData() ?? [];
                    $locationProcessedList = [];

                    $this->info('    - ' . count($ocpiLocationList) . ' Location(s) retrieved');

                    foreach ($ocpiLocationList as $ocpiLocation) {
                        $ocpiLocationId = $ocpiLocation['id'] ?? null;

                        DB::connection(config('ocpi.database.connection'))->beginTransaction();

                        $location = $this->searchLocation(
                            $partyRole,
                            $ocpiLocationId,
                        );

                        $this->info(
                            '      > Processing ' . ($location === null ? 'new' : 'existing') . ' Location ' . $ocpiLocationId
                        );

                        // New Location.
                        if ($location === null) {
                            if (!$this->locationCreate(
                                $partyRole,
                                $ocpiLocationId,
                                $ocpiLocation,
                            )) {
                                $hasError = true;
                                $this->error('Error creating Location ' . $ocpiLocationId . '.');

                                DB::connection(config('ocpi.database.connection'))->rollback();

                                continue;
                            }
                        } else {
                            // Replaced Location.
                            if (!$this->locationReplace(
                                $location,
                                $ocpiLocation
                            )) {
                                $hasError = true;
                                $this->error('Error replacing Location ' . $ocpiLocationId . '.');

                                DB::connection(config('ocpi.database.connection'))->rollback();

                                continue;
                            }
                        }

                        $locationProcessedList[] = $ocpiLocationId;

                        DB::connection(config('ocpi.database.connection'))->commit();
                    }
                    $offset += $limit;
                } while (null !== $ocpiLocationList->getLink());


                $this->info('    - ' . count($locationProcessedList) . ' Location(s) synchronized');
            }

            return $hasError
                ? Command::FAILURE
                : Command::SUCCESS;
        }
    }
}
