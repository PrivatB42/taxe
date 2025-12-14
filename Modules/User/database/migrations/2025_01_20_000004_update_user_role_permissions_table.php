<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier que la table existe et que la colonne role_id n'existe pas encore
        if (Schema::hasTable('user_role_permissions') && !Schema::hasColumn('user_role_permissions', 'role_id')) {
            // Supprimer l'ancienne contrainte unique si elle existe
            try {
                Schema::table('user_role_permissions', function (Blueprint $table) {
                    $table->dropUnique(['role', 'permission_id']);
                });
            } catch (\Exception $e) {
                // La contrainte n'existe peut-être pas, continuer
            }

            // Ajouter la nouvelle colonne role_id
            Schema::table('user_role_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable()->after('id');
            });

            // Migrer les données : convertir les codes de rôles en IDs
            if (Schema::hasColumn('user_role_permissions', 'role')) {
                $rolePermissions = DB::table('user_role_permissions')->get();
                
                foreach ($rolePermissions as $rp) {
                    $role = DB::table('user_roles')->where('code', $rp->role)->first();
                    if ($role) {
                        DB::table('user_role_permissions')
                            ->where('id', $rp->id)
                            ->update(['role_id' => $role->id]);
                    }
                }

                // Supprimer les enregistrements sans rôle valide
                DB::table('user_role_permissions')->whereNull('role_id')->delete();

                // Modifier la colonne role_id pour qu'elle ne soit plus nullable
                Schema::table('user_role_permissions', function (Blueprint $table) {
                    $table->unsignedBigInteger('role_id')->nullable(false)->change();
                });

                // Supprimer l'ancienne colonne role
                Schema::table('user_role_permissions', function (Blueprint $table) {
                    $table->dropColumn('role');
                });
            }

            // Ajouter la contrainte de clé étrangère
            Schema::table('user_role_permissions', function (Blueprint $table) {
                $table->foreign('role_id')->references('id')->on('user_roles')->onDelete('cascade');
            });

            // Nouvelle contrainte unique
            Schema::table('user_role_permissions', function (Blueprint $table) {
                $table->unique(['role_id', 'permission_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('user_role_permissions', 'role_id')) {
            Schema::table('user_role_permissions', function (Blueprint $table) {
                $table->dropForeign(['role_id']);
                $table->dropUnique(['role_id', 'permission_id']);
                
                // Ajouter la colonne role
                $table->string('role')->after('id');
            });

            // Migrer les données de role_id vers role (code)
            $rolePermissions = DB::table('user_role_permissions')->get();
            foreach ($rolePermissions as $rp) {
                $role = DB::table('user_roles')->find($rp->role_id);
                if ($role) {
                    DB::table('user_role_permissions')
                        ->where('id', $rp->id)
                        ->update(['role' => $role->code]);
                }
            }

            Schema::table('user_role_permissions', function (Blueprint $table) {
                // Supprimer role_id
                $table->dropColumn('role_id');
                
                // Restaurer l'ancienne contrainte unique
                $table->unique(['role', 'permission_id']);
            });
        }
    }
};

