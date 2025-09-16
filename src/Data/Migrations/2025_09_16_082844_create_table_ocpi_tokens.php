<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(config('ocpi.database.table.prefix').'tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('party_id');
            $table->string('token_a')->nullable();
            $table->string('token_b')->nullable();
            $table->string('token_c')->nullable();
            $table->tinyInteger('is_registered')->default(0);
            $table->timestamps();
            $table->foreign('party_id')->references('id')->on(config('ocpi.database.table.prefix').'parties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('ocpi.database.table.prefix').'tokens');
    }
};
