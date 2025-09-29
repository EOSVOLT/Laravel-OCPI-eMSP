<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Money\Money;
use Ocpi\Models\Tariff\Tariff;
use Ocpi\Models\Tariff\TariffElements;
use Ocpi\Models\Tariff\TariffPriceComponents;
use Ocpi\Modules\Tariffs\Enums\TariffDimensionType;

return new class extends Migration {
    public function up(): void
    {
        $tariff = Tariff::query()->create([
            'external_id' => 'free_of_charge',
            'currency' => Money::EUR(0)->getCurrency()->getCode(),
        ]);
        $priceComponents = TariffPriceComponents::query()->create([
            'dimension_type' => TariffDimensionType::FLAT,
            'price' => 0,
            'step_size' => 1
        ]);
        /** @var TariffElements $element */
        $element = $tariff->elements()->create();
        $element->priceComponents()->create([
            'tariff_price_component_id' => $priceComponents->id,
        ]);
    }

    public function down(): void
    {
        
    }
};
