<?php

namespace Ocpi\Modules\Locations\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Ocpi\Support\Client\Client;
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
            ->withWhereHas('party', function (Builder $query) use ($optionParty) {
                $query->when($optionParty, function (Builder $query) use ($optionParty) {
                    $query->whereIn('code', explode(',', $optionParty));
                });
                $query->where('is_external_party', true);
            })->withWhereHas('tokens', function (Builder $query) {
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
            $this->info('  - Processing Party '.$party->code);

            $ocpiClient = new Client($party, 'locations');

            if (empty($ocpiClient->resolveBaseUrl())) {
                $this->warn('Party '.$party->code.' is not configured to use the Locations module.');

                continue;
            }

            foreach ($party->roles as $partyRole) {
                $this->info(
                    '    - Call '.$partyRole->code.' / '.$partyRole->country_code.' - OCPI - Locations GET'
                );
                $offset = 0;
                do {
                    $ocpiLocationList = $ocpiClient->locations()->get(offset: $offset, limit: 100);
                    $locationProcessedList = [];

                    $this->info('    - '.count($ocpiLocationList).' Location(s) retrieved');

                    foreach ($ocpiLocationList as $ocpiLocation) {
                        $ocpiLocationId = $ocpiLocation['id'] ?? null;

                        DB::connection(config('ocpi.database.connection'))->beginTransaction();

                        $location = $this->searchByExternalId(
                            $partyRole,
                            $ocpiLocationId,
                        );

                        $this->info(
                            '      > Processing '.($location === null ? 'new' : 'existing').' Location '.$ocpiLocationId
                        );

                        // New Location.
                        if ($location === null) {
                            if (!$this->locationCreate(
                                payload: $ocpiLocation,
                                party_role_id: $partyRole->id,
                                location_id: $ocpiLocationId,
                            )) {
                                $hasError = true;
                                $this->error('Error creating Location '.$ocpiLocationId.'.');

                                DB::connection(config('ocpi.database.connection'))->rollback();

                                continue;
                            }
                        } else {
                            // Replaced Location.
                            if (!$this->locationReplace(
                                payload: $ocpiLocation,
                                location: $location,
                            )) {
                                $hasError = true;
                                $this->error('Error replacing Location '.$ocpiLocationId.'.');

                                DB::connection(config('ocpi.database.connection'))->rollback();

                                continue;
                            }
                        }

                        $locationProcessedList[] = $ocpiLocationId;

                        DB::connection(config('ocpi.database.connection'))->commit();
                    }
                } while (true);


                $this->info('    - '.count($locationProcessedList).' Location(s) synchronized');
            }

            return $hasError
                ? Command::FAILURE
                : Command::SUCCESS;
        }
    }
}
