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
        Schema::create('paiement_paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribuable_taxe_id');
            $table->foreignId('caisse_id');
            $table->foreignId('caissier_id');
            $table->string('reference')->unique();
            $table->decimal('montant', 20, 2);
            $table->decimal('montant_encaisse', 20, 2);
            $table->decimal('montant_rendu', 20, 2);
            $table->dateTime('date_paiement')->useCurrent();
            $table->dateTime('date_activement')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiement_paiements');
    }
};
