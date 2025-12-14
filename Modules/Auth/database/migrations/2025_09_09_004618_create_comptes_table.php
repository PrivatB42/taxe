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
        Schema::create('auth_comptes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personne_id');
            $table->string('numero_compte')->unique();
            $table->string('password');
            $table->string('type_compte'); //gestionnaire, contribuable
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_comptes');
    }
};
