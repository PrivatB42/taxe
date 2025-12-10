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
        Schema::create('paiement_caisses_gestionnaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caisse_id');
            $table->foreignId('gestionnaire_id');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiement_caisses_gestionnaires');
    }
};
