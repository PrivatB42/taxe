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
        Schema::create('user_contribuables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personne_id')->unique();
            $table->foreignId('commune_id');
            $table->string('matricule')->unique();
            $table->string('adresse_complete')->nullable();
             $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_contribuables');
    }
};
