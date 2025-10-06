<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(config('ocpi.database.table.prefix') . 'tariff_price_components', function (Blueprint $table) {
            $table->decimal('price_excl_vat', 20,5);
            $table->decimal('price_incl_vat', 20,5)->nullable();
            $table->dropUnique('tariff_price_components_unique');
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
            $table->dropColumn('price_excl_vat');
            $table->dropColumn('price_incl_vat');
            $table->decimal('price', 20,5);
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
