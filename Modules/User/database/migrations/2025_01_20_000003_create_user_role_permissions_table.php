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
        // Si la table existe déjà avec l'ancienne structure, la migration 000004 s'en chargera
        if (!Schema::hasTable('user_role_permissions')) {
            Schema::create('user_role_permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained('user_roles')->onDelete('cascade');
                $table->foreignId('permission_id')->constrained('user_permissions')->onDelete('cascade');
                $table->timestamps();
                
                $table->unique(['role_id', 'permission_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_role_permissions');
    }
};

