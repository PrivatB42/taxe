<?php

use App\Helpers\Constantes;
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
        Schema::create('user_contribuables_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribuable_id')->index();
            $table->foreignId('taxe_id')->index();
            $table->foreignId('activite_id')->index();
            $table->foreignId('exercice_id')->index();
            $table->decimal('montant', 10, 2);
            $table->decimal('montant_a_payer', 20, 2);
            $table->decimal('montant_paye', 20, 2);
            $table->string('statut')->default(Constantes::STATUT_NON_PAYE)->index(); //['paye', 'non_paye']
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['contribuable_id', 'taxe_id', 'activite_id', 'exercice_id'], 'contribuable_taxe_activite_exercice_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_contribuables_parametres');
    }
};
