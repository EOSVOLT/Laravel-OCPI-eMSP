<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ocpi\Models\Tariff\TariffPriceComponents;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'tariff_price_components', function (Blueprint $table) {
            $table->decimal('price_excl_vat', 20,5)->after('dimension_type');
            $table->decimal('price_incl_vat', 20,5)->nullable()->after('price_excl_vat');
            $table->dropUnique('tariff_price_components_unique');
            TariffPriceComponents::query()->get()->each(function (TariffPriceComponents $component) {
                $component->update(['price_excl_vat' => $component->price]);
            });
            $table->dropColumn('price');
            $table->unique(
                [
                    'dimension_type',
                    'price_excl_vat',
                    'vat',
                    'step_size',
                ],
                'tariff_price_components_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'tariffs', function (Blueprint $table) {
            $table->dropUnique('tariff_price_components_unique');
            $table->decimal('price', 20,5);
            TariffPriceComponents::query()->get()->each(function (TariffPriceComponents $component) {
                $component->update(['price' => $component->price_excl_vat]);
            });
            $table->dropColumn('price_excl_vat');
            $table->dropColumn('price_incl_vat');
            $table->unique(
                [
                    'dimension_type',
                    'price',
                    'vat',
                    'step_size',
                ],
                'tariff_price_components_unique'
            );
        });
    }
};
