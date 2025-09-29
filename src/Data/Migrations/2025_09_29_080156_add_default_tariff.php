<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ocpi\Models\Tariff\Tariff;
use Ocpi\Models\Tariff\TariffPriceComponents;
use Ocpi\Modules\Tariffs\Enums\TariffDimensionType;

return new class extends Migration {
    public function up(): void
    {
        $tariff = Tariff::query()->create([
            'external_id' => 'free_of_charge',
            'currency' => 'EUR',
        ]);
        $priceComponents = TariffPriceComponents::query()->create([
            'dimenstion_type' => TariffDimensionType::FLAT,
            'price' => 0,
            'step_size' => 1
        ]);
        /** @var \Ocpi\Models\Tariff\TariffElements $element */
        $element = $tariff->elements()->create();
        $element->priceComponents()->create([
            'tariff_price_component_id' => $priceComponents->id,
        ]);
    }

    public function down(): void
    {
        
    }
};
