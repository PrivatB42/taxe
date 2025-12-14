<?php

namespace Modules\User\Database\Seeders;

use App\Helpers\Constantes;
use Illuminate\Database\Seeder;
use Modules\User\Models\Permission;
use Modules\User\Models\RolePermission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $output = $this->command ?? new class {
            public function info($msg) { echo $msg . "\n"; }
            public function warn($msg) { echo $msg . "\n"; }
            public function error($msg) { echo $msg . "\n"; }
        };
        
        $output->info('📋 Création des permissions...');
        
        // Créer toutes les permissions
        $permissionCount = 0;
        foreach (Constantes::PERMISSIONS as $code => $nom) {
            $permission = Permission::firstOrCreate(
                ['code' => $code],
                [
                    'nom' => $nom,
                    'description' => $nom,
                ]
            );
            if ($permission->wasRecentlyCreated) {
                $permissionCount++;
            }
        }
        $output->info("✓ {$permissionCount} nouvelle(s) permission(s) créée(s)");

        $output->info('🔐 Attribution des permissions aux rôles...');
        
        // Vérifier que les rôles existent dans la table
        $requiredRoles = array_keys(Constantes::ROLE_PERMISSIONS);
        $existingRoles = \Modules\User\Models\Role::whereIn('code', $requiredRoles)->pluck('code')->toArray();
        $missingRoles = array_diff($requiredRoles, $existingRoles);
        
        if (!empty($missingRoles)) {
            $output->warn('⚠️  Certains rôles n\'existent pas encore dans la table : ' . implode(', ', $missingRoles));
            $output->info('💡 Les permissions seront attribuées aux rôles existants uniquement');
        }
        
        // Créer les permissions par rôle
        foreach (Constantes::ROLE_PERMISSIONS as $roleCode => $permissionCodes) {
            // Vérifier que le rôle existe dans la table
            $roleModel = \Modules\User\Models\Role::where('code', $roleCode)->first();
            if (!$roleModel) {
                $output->warn("⚠️  Rôle '{$roleCode}' n'existe pas dans la table, permissions ignorées");
                continue;
            }
            
            // Si c'est l'admin, on lui donne toutes les permissions
            if ($roleCode === Constantes::ROLE_ADMIN) {
                $permissionIds = Permission::pluck('id')->toArray();
            } else {
                $permissionIds = Permission::whereIn('code', $permissionCodes)->pluck('id')->toArray();
            }

            // Supprimer les permissions existantes
            RolePermission::where('role_id', $roleModel->id)->delete();

            // Ajouter les nouvelles permissions
            foreach ($permissionIds as $permissionId) {
                RolePermission::create([
                    'role_id' => $roleModel->id,
                    'permission_id' => $permissionId,
                ]);
            }
            
            $output->info("✓ Rôle '{$roleModel->nom}' ({$roleCode}) : " . count($permissionIds) . " permission(s)");
        }
        
        $output->info('✅ Permissions initialisées avec succès !');
    }
}

