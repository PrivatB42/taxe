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
        Schema::create('entite_activites_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activite_id');
            $table->foreignId('taxe_id');
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['activite_id', 'taxe_id'], 'activite_taxe_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entite_activites_taxes');
    }
};
