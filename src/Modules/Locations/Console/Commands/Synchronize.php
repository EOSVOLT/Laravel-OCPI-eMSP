<?php

namespace Ocpi\Modules\Locations\Console\Commands;

use Illuminate\Console\Command;
use Ocpi\Modules\Locations\Traits\HandlesLocation;
use Throwable;

class Synchronize extends Command
{
    use HandlesLocation;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ocpi:locations:synchronize {--P|party= : Party Code to synchronize}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize locations';

    /**
     * Execute the console command.
     * @throws Throwable
     */
    public function handle(): void
    {
        $optionParty = $this->option('party');
        $this->fetchLocationFromCPO($optionParty);
    }
}
