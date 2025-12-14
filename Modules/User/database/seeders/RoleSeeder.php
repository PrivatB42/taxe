<?php

namespace Modules\User\Database\Seeders;

use App\Helpers\Constantes;
use Illuminate\Database\Seeder;
use Modules\User\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $output = $this->command ?? new class {
            public function info($msg) { echo $msg . "\n"; }
            public function warn($msg) { echo "⚠️  " . $msg . "\n"; }
        };

        $output->info('👤 Création des rôles par défaut...');

        $roles = [
            [
                'code' => Constantes::ROLE_ADMIN,
                'nom' => 'Admin',
                'description' => 'Administrateur avec toutes les permissions du système',
                'is_active' => true,
            ],
            [
                'code' => Constantes::ROLE_REGISSEUR,
                'nom' => 'Régisseur',
                'description' => 'Régisseur avec tous les droits des agents de la Régie, gestion des utilisateurs, caisses, tableau de bord et reportings',
                'is_active' => true,
            ],
            [
                'code' => Constantes::ROLE_AGENT_DE_LA_REGIE,
                'nom' => 'Agent de la Régie',
                'description' => 'Agent de la Régie : création et gestion des taxes, contribuables, activités taxables, caisses et caissiers',
                'is_active' => true,
            ],
            [
                'code' => Constantes::ROLE_CAISSIER,
                'nom' => 'Caissier',
                'description' => 'Caissier : ouverture/fermeture de caisse, encaissement et impression de reçus',
                'is_active' => true,
            ],
            [
                'code' => Constantes::ROLE_SUPERVISEUR,
                'nom' => 'Superviseur',
                'description' => 'Superviseur : accès au tableau de bord et aux reportings',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['code' => $roleData['code']],
                $roleData
            );
            
            if ($role->wasRecentlyCreated) {
                $output->info("✓ Rôle créé : {$roleData['nom']} ({$roleData['code']})");
            } else {
                // Mettre à jour le rôle existant si nécessaire
                $role->update([
                    'nom' => $roleData['nom'],
                    'description' => $roleData['description'],
                    'is_active' => $roleData['is_active'],
                ]);
                $output->info("→ Rôle mis à jour : {$roleData['nom']} ({$roleData['code']})");
            }
        }

        $output->info('✅ Rôles initialisés avec succès !');
    }
}

