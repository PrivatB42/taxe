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
        Schema::create('user_personnes', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet');
            $table->string('slug')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('telephone')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_personnes');
    }
};
