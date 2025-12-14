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
        Schema::create('entite_taxes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique(); // ex: ODP, PATENTE

            // Exemple: "surface_longueur * surface_largeur * coefficient"
            $table->text('formule')->nullable();
            $table->integer('multiplicateur')->default(12);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entite_taxes');
    }
};
