<?php

namespace Ocpi\Modules\Tariffs\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class TariffRemove implements ShouldDispatchAfterCommit
{
    use Dispatchable;
}