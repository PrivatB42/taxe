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
        Schema::create('entite_taxes_constantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxe_id');

            $table->string('nom');  // exemple: 'coef1', 'coef2'
            $table->string('valeur');
            $table->string('type')->default(Constantes::TYPE_DECIMAL); //['int', 'decimal', 'string', 'bool']
            $table->boolean('is_active')->default(false);

            $table->timestamps();

            $table->unique(['taxe_id', 'nom']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entite_taxes_constantes');
    }
};
