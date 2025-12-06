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
        Schema::create('user_contribuables_activites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribuable_id');
            $table->foreignId('activite_id');
            $table->boolean('is_active')->default(false);
            $table->year('annee_debut');
            $table->timestamps();

            $table->unique(['contribuable_id', 'activite_id'], 'contribuable_activite_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_contribuables_activites');
    }
};
