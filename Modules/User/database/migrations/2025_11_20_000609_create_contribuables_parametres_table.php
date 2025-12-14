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
        Schema::create('user_contribuables_parametres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribuable_activite_id');

            $table->string('nom'); // surface_longueur, surface_largeur, nb_employes...
            $table->string('valeur')->nullable();
            $table->string('type')->default(Constantes::TYPE_DECIMAL); //['int', 'decimal', 'string', 'bool']
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['contribuable_activite_id', 'nom'], 'contrib_param_activite_nom_unique');
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
