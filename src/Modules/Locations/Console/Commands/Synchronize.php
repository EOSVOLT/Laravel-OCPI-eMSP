<?php

namespace Ocpi\Modules\Locations\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Ocpi\Models\Locations\Location;
use Ocpi\Models\Locations\LocationConnector;
use Ocpi\Models\Locations\LocationEvse;
use Ocpi\Models\Party;
use Ocpi\Support\Client\Client;

class Synchronize extends Command
{
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

        $partyList = Party::with(['roles'])
            ->registered()
            ->when($optionParty, function (Builder $query) use ($optionParty) {
                $query->whereIn('code', explode(',', $optionParty));
            })
            ->get();

        if ($optionParty !== null && $partyList->count() !== count(explode(',', $optionParty))) {
            $this->error('Requested Party list could not be found.');

            return Command::FAILURE;
        }

        if ($partyList->pluck('roles')->flatten()->count() === 0) {
            $this->error('No Party to process.');

            return Command::FAILURE;
        }

        foreach ($partyList as $party) {
            $this->info('  - Processing Party '.$party->code);

            $ocpiClient = new Client($party, 'locations');

            if (empty($ocpiClient->resolveBaseUrl())) {
                $this->warn('Party '.$party->code.' is not configured to use the Locations module.');

                continue;
            }

            foreach ($party->roles as $partyRole) {
                $this->info('    - Call '.$partyRole->code.' / '.$partyRole->country_code.' - OCPI - Locations GET');
                $ocpiLocationList = $ocpiClient->locations()->all();

                $locationProcessedList = [];
                $locationEvseProcessedList = [];
                $locationConnectorProcessedList = [];

                $this->info('    - '.count($ocpiLocationList).' Location(s) retrieved');

                foreach ($ocpiLocationList as $ocpiLocation) {
                    DB::beginTransaction();

                    $location = Location::firstOrNew([
                        'party_role_id' => $partyRole->id,
                        'id' => $ocpiLocation?->id,
                    ]);

                    $ocpiLocationEvseList = $ocpiLocation->evses;
                    unset($ocpiLocation->evses);

                    $location->object = $ocpiLocation;
                    if (! $location->save()) {
                        $this->error('Error saving Location '.$ocpiLocation?->id.'.');

                        DB::rollBack();

                        continue;
                    }

                    $locationProcessedList[] = $location->id;

                    if (! is_array($ocpiLocationEvseList) || count($ocpiLocationEvseList) === 0) {
                        $this->warn('Location '.$ocpiLocation?->id.' without EVSE.');
                    }

                    foreach (($ocpiLocationEvseList ?? []) as $ocpiLocationEvse) {
                        $locationEvse = LocationEvse::firstOrNew([
                            'location_id' => $ocpiLocation?->id,
                            'uid' => $ocpiLocationEvse?->uid,
                        ]);

                        $ocpiLocationEvseConnectorList = $ocpiLocationEvse->connectors;
                        unset($ocpiLocationEvse->connectors);

                        //                        $locationEvse->setCompositeId();
                        $locationEvse->object = $ocpiLocationEvse;
                        if (! $locationEvse->save()) {
                            $this->error('Error saving EVSE '.$ocpiLocationEvse?->uid.'.');

                            DB::rollBack();

                            continue;
                        }

                        $locationEvseProcessedList[] = $ocpiLocationEvse->uid;

                        if (! is_array($ocpiLocationEvseConnectorList) || count($ocpiLocationEvseConnectorList) === 0) {
                            $this->warn('EVSE '.$ocpiLocation?->id.' without Connector.');
                        }

                        foreach (($ocpiLocationEvseConnectorList ?? []) as $ocpiLocationEvseConnector) {
                            $locationConnector = LocationConnector::firstOrNew([
                                'location_evse_composite_id' => $locationEvse?->composite_id,
                                'id' => $ocpiLocationEvseConnector?->id,
                            ]);

                            $locationConnector->object = $ocpiLocationEvseConnector;
                            if (! $locationConnector->save()) {
                                $this->error('Error saving Connector '.$locationConnector?->id.'.');

                                DB::rollBack();

                                continue;
                            }

                            $locationConnectorProcessedList[] = $ocpiLocationEvseConnector->id;
                        }
                    }

                    DB::commit();
                }

                $this->info('    - '.count($locationProcessedList).' Location(s) synchronized');
            }
        }

        return Command::SUCCESS;
    }
}
