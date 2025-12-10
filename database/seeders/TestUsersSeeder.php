<?php

namespace Database\Seeders;

use App\Helpers\Constantes;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestUsersSeeder extends Seeder
{
    /**
     * Créer les utilisateurs de test:
     * - 1 Administrateur
     * - 1 Superviseur
     * - 1 Gestionnaire
     */
    public function run(): void
    {
        // ===========================================
        // 1. ADMINISTRATEUR
        // ===========================================
        $adminPersonneId = DB::table('user_personnes')->insertGetId([
            'nom_complet' => 'Admin Système',
            'slug' => 'admin-systeme-' . Str::random(8),
            'email' => 'admin@taxe.local',
            'telephone' => '0700000001',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('auth_comptes')->insert([
            'personne_id' => $adminPersonneId,
            'numero_compte' => 'ADM-2024-0001',
            'password' => Hash::make('admin123'),
            'type_compte' => Constantes::COMPTE_ADMIN,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✓ Administrateur créé: admin@taxe.local / admin123');

        // ===========================================
        // 2. SUPERVISEUR
        // ===========================================
        $supPersonneId = DB::table('user_personnes')->insertGetId([
            'nom_complet' => 'Jean Superviseur',
            'slug' => 'jean-superviseur-' . Str::random(8),
            'email' => 'superviseur@taxe.local',
            'telephone' => '0700000002',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('auth_comptes')->insert([
            'personne_id' => $supPersonneId,
            'numero_compte' => 'SUP-2024-0001',
            'password' => Hash::make('superviseur123'),
            'type_compte' => Constantes::COMPTE_SUPERVISEUR,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✓ Superviseur créé: superviseur@taxe.local / superviseur123');

        // ===========================================
        // 3. GESTIONNAIRE
        // ===========================================
        $gesPersonneId = DB::table('user_personnes')->insertGetId([
            'nom_complet' => 'Marie Gestionnaire',
            'slug' => 'marie-gestionnaire-' . Str::random(8),
            'email' => 'gestionnaire@taxe.local',
            'telephone' => '0700000003',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('auth_comptes')->insert([
            'personne_id' => $gesPersonneId,
            'numero_compte' => 'GES-2024-0001',
            'password' => Hash::make('gestionnaire123'),
            'type_compte' => Constantes::COMPTE_GESTIONNAIRE,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Créer l'entrée gestionnaire avec commune_id = 1
        DB::table('user_gestionnaires')->insert([
            'personne_id' => $gesPersonneId,
            'commune_id' => Constantes::COMMUNE_ID,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✓ Gestionnaire créé: gestionnaire@taxe.local / gestionnaire123');

        // ===========================================
        // RÉSUMÉ
        // ===========================================
        $this->command->newLine();
        $this->command->info('╔═══════════════════════════════════════════════════════════╗');
        $this->command->info('║              UTILISATEURS DE TEST CRÉÉS                   ║');
        $this->command->info('╠═══════════════════════════════════════════════════════════╣');
        $this->command->info('║  ADMIN                                                    ║');
        $this->command->info('║  Email: admin@taxe.local                                  ║');
        $this->command->info('║  Mot de passe: admin123                                   ║');
        $this->command->info('║  N° Compte: ADM-2024-0001                                 ║');
        $this->command->info('╠═══════════════════════════════════════════════════════════╣');
        $this->command->info('║  SUPERVISEUR                                              ║');
        $this->command->info('║  Email: superviseur@taxe.local                            ║');
        $this->command->info('║  Mot de passe: superviseur123                             ║');
        $this->command->info('║  N° Compte: SUP-2024-0001                                 ║');
        $this->command->info('╠═══════════════════════════════════════════════════════════╣');
        $this->command->info('║  GESTIONNAIRE                                             ║');
        $this->command->info('║  Email: gestionnaire@taxe.local                           ║');
        $this->command->info('║  Mot de passe: gestionnaire123                            ║');
        $this->command->info('║  N° Compte: GES-2024-0001                                 ║');
        $this->command->info('╚═══════════════════════════════════════════════════════════╝');
    }
}


