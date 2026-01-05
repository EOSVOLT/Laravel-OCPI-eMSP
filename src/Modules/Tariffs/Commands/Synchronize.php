<?php

namespace Ocpi\Modules\Tariffs\Commands;

use Illuminate\Console\Command;
use Ocpi\Modules\Tariffs\Traits\HandlesTariff;
use Throwable;

class Synchronize extends Command
{
    use HandlesTariff;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ocpi:tariffs:synchronize {--P|party= : Party Code to synchronize}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Tariffs';

    /**
     * Execute the console command.
     * @throws Throwable
     */
    public function handle(): void
    {
        $optionParty = $this->option('party');
        $this->fetchTariffFromCPO($optionParty);
    }
}