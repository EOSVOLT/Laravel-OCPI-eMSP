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
        Schema::create(config('ocpi.database.table.prefix') . 'tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('external_id', 36);
            $table->string('currency', 3);
            $table->string('type');
            $table->json('tariff_alt_text')->nullable();
            $table->string('tariff_alt_url')->nullable();
            $table->decimal('min_price_excl_vat', 20, 5)->nullable();
            $table->decimal('min_price_incl_vat', 20, 5)->nullable();
            $table->decimal('max_price_excl_vat', 20, 5)->nullable();
            $table->decimal('max_price_incl_vat', 20, 5)->nullable();
            $table->timestamps();

            $table->unique(
                [
                    'external_id',
                    'currency',
                    'type',
                    'min_price_excl_vat',
                    'min_price_incl_vat',
                    'max_price_excl_vat',
                    'max_price_incl_vat',
                ],
                'tariff_unique'
            );
        });
        Schema::create(config('ocpi.database.table.prefix') . 'tariff_restrictions', function (Blueprint $table) {
            $table->id();
            $table->string('start_time', 5)->nullable();
            $table->string('end_time', 5)->nullable();
            $table->string('start_date', 10)->nullable();
            $table->string('end_date', 10)->nullable();
            $table->decimal('min_kwh', 20, 5)->nullable();
            $table->decimal('max_kwh', 20, 5)->nullable();
            $table->decimal('min_current', 20, 5)->nullable();
            $table->decimal('max_current', 20, 5)->nullable();
            $table->decimal('min_power', 20, 5)->nullable();
            $table->decimal('max_power', 20, 5)->nullable();
            $table->integer('min_duration')->nullable();
            $table->integer('max_duration')->nullable();
            $table->json('day_of_week')->nullable();
            $table->string('reservation')->nullable();
            $table->timestamps();

            $table->unique([
                'start_time',
                'end_time',
                'start_date',
                'end_date',
                'min_kwh',
                'max_kwh',
                'min_current',
                'max_current',
                'min_power',
                'max_power',
                'min_duration',
                'max_duration',
                'day_of_week',
                'reservation',
            ], 'tariff_restrictions_unique');
        });
        Schema::create(config('ocpi.database.table.prefix') . 'tariff_price_components', function (Blueprint $table) {
            $table->id();
            $table->string('dimension_type');
            $table->decimal('price', 20, 5);
            $table->decimal('vat', 20, 5)->nullable();
            $table->integer('step_size')->default(1);
            $table->timestamps();
            $table->unique(['tariff_id', 'dimension_type', 'vat', 'step_size'], 'tariff_price_components_unique');
        });

        Schema::table(config('ocpi.database.table.prefix') . 'tariff_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tariff_id')->constrained(
                config('ocpi.database.table.prefix') . 'tariffs',
                'id'
            )->cascadeOnDelete();
            $table->foreignId('tariff_restriction_id')->nullable()->constrained(
                config('ocpi.database.table.prefix') . 'tariff_restrictions',
                'id'
            )->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['tariff_id', 'tariff_restriction_id'], 'tariff_elements_unique');
        });
        Schema::table(
            config('ocpi.database.table.prefix') . 'tariff_element_price_components',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('tariff_element_id')->constrained(
                    config('ocpi.database.table.prefix') . 'tariff_elements',
                    'id'
                )->cascadeOnDelete();
                $table->foreignId('tariff_price_component_id')->constrained(
                    config('ocpi.database.table.prefix') . 'tariff_price_components',
                    'id'
                )->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['tariff_element_id', 'tariff_price_component_id'],
                    'tariff_element_price_components_unique');
            }
        );
        Schema::table(config('ocpi.database.table.prefix') . 'tariff_parties', function (Blueprint $table) {
            $table->foreignId('tariff_id')->constrained(config('ocpi.database.table.prefix') . 'tariffs', 'id')->cascadeOnDelete();
            $table->foreignId('party_id')->constrained(config('ocpi.database.table.prefix') . 'parties', 'id')->cascadeOnDelete();
            $table->unique(['tariff_id', 'party_id'], 'tariff_parties_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('ocpi.database.table.prefix') . 'tariff_price_components');
        Schema::dropIfExists(config('ocpi.database.table.prefix') . 'tariffs');
        Schema::dropIfExists(config('ocpi.database.table.prefix') . 'tariff_restrictions');
        Schema::dropIfExists(config('ocpi.database.table.prefix') . 'tariff_elements');
        Schema::dropIfExists(config('ocpi.database.table.prefix') . 'tariff_element_price_components');
    }
};
