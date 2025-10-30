<?php

namespace Ocpi\Modules\Tariffs\DTO;

use Carbon\Carbon;
use Ocpi\Modules\Tariffs\Objects\Tariff;
use Ocpi\Modules\Tariffs\Objects\TariffElementCollection;

class TariffDTO extends Tariff
{

    public function __construct(
        string $countryCode,
        string $party_code,
        string $external_id,
        string $currency,
        TariffElementCollection $elements,
        Carbon $lastUpdated,
    ) {
        parent::__construct(-1, $countryCode, $party_code, $external_id, $currency, $elements, $lastUpdated);
    }

}