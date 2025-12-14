<?php

namespace Modules\User\Database\Seeders;

use App\Helpers\Constantes;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Models\Compte;
use Modules\Auth\Services\CompteService;
use Modules\User\Models\Gestionnaire;
use Modules\User\Models\Personne;
use Modules\User\Services\PersonneService;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $output = $this->command ?? new class {
            public function info($msg) { echo $msg . "\n"; }
            public function error($msg) { echo "❌ " . $msg . "\n"; }
        };

        // Vérifier que les rôles existent dans la table
        $output->info('🔍 Vérification des rôles...');
        $requiredRoles = [
            Constantes::ROLE_ADMIN,
            Constantes::ROLE_REGISSEUR,
            Constantes::ROLE_AGENT_DE_LA_REGIE,
            Constantes::ROLE_CAISSIER,
            Constantes::ROLE_SUPERVISEUR,
        ];

        $missingRoles = [];
        foreach ($requiredRoles as $roleCode) {
            $role = \Modules\User\Models\Role::where('code', $roleCode)->first();
            if (!$role) {
                $missingRoles[] = $roleCode;
            }
        }

        if (!empty($missingRoles)) {
            $output->error('Les rôles suivants n\'existent pas dans la base de données : ' . implode(', ', $missingRoles));
            $output->info('Veuillez d\'abord exécuter le RoleSeeder : php artisan db:seed --class="Modules\\User\\Database\\Seeders\\RoleSeeder"');
            throw new \Exception('Rôles manquants dans la base de données');
        }

        $output->info('✅ Tous les rôles sont présents');
        $output->info('👥 Création des utilisateurs de test...');

        $personneService = app(PersonneService::class);
        $compteService = app(CompteService::class);
        
        $password = 'password123'; // Mot de passe par défaut pour tous les utilisateurs de test

        $testUsers = [
            [
                'nom_complet' => 'Administrateur Système',
                'telephone' => '0100000001',
                'email' => 'admin@test.com',
                'role' => Constantes::ROLE_ADMIN,
            ],
            [
                'nom_complet' => 'Jean Régisseur',
                'telephone' => '0100000002',
                'email' => 'regisseur@test.com',
                'role' => Constantes::ROLE_REGISSEUR,
            ],
            [
                'nom_complet' => 'Marie Agent',
                'telephone' => '0100000003',
                'email' => 'agent@test.com',
                'role' => Constantes::ROLE_AGENT_DE_LA_REGIE,
            ],
            [
                'nom_complet' => 'Pierre Caissier',
                'telephone' => '0100000004',
                'email' => 'caissier@test.com',
                'role' => Constantes::ROLE_CAISSIER,
            ],
            [
                'nom_complet' => 'Sophie Superviseur',
                'telephone' => '0100000005',
                'email' => 'superviseur@test.com',
                'role' => Constantes::ROLE_SUPERVISEUR,
            ],
        ];

        DB::beginTransaction();
        
        try {
            foreach ($testUsers as $userData) {
                // Vérifier si l'utilisateur existe déjà
                $existingPersonne = Personne::where('email', $userData['email'])
                    ->orWhere('telephone', $userData['telephone'])
                    ->first();

                if ($existingPersonne) {
                    $output->info("→ Utilisateur {$userData['email']} existe déjà, mise à jour...");
                    
                    // Mettre à jour la personne
                    $existingPersonne->update([
                        'nom_complet' => $userData['nom_complet'],
                        'telephone' => $userData['telephone'],
                        'email' => $userData['email'],
                    ]);

                    $personne = $existingPersonne;
                } else {
                    // Créer la personne
                    $personne = $personneService->store([
                        'nom_complet' => $userData['nom_complet'],
                        'telephone' => $userData['telephone'],
                        'email' => $userData['email'],
                    ]);
                }

                // Vérifier si le compte existe
                $existingCompte = Compte::where('personne_id', $personne->id)->first();
                
                if (!$existingCompte) {
                    // Créer le compte
                    $compteService->create(
                        $personne->id,
                        Constantes::COMPTE_GESTIONNAIRE,
                        $password
                    );
                } else {
                    // Mettre à jour le mot de passe
                    $existingCompte->update([
                        'password' => Hash::make($password),
                        'is_active' => true,
                    ]);
                }

                // Vérifier si le gestionnaire existe
                $existingGestionnaire = Gestionnaire::where('personne_id', $personne->id)->first();
                
                if (!$existingGestionnaire) {
                    // Créer le gestionnaire
                    Gestionnaire::create([
                        'personne_id' => $personne->id,
                        'commune_id' => Constantes::COMMUNE_ID,
                        'role' => $userData['role'],
                        'is_active' => true,
                    ]);
                } else {
                    // Mettre à jour le gestionnaire
                    $existingGestionnaire->update([
                        'role' => $userData['role'],
                        'is_active' => true,
                    ]);
                }

                $output->info("✓ Utilisateur créé/mis à jour : {$userData['nom_complet']} ({$userData['role']})");
            }

            DB::commit();
            $output->info("\n✅ Tous les utilisateurs de test ont été créés avec succès !");
            $output->info("📧 Email/Téléphone : Utilisez l'email ou le téléphone pour vous connecter");
            $output->info("🔑 Mot de passe : password123");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $output->error("Erreur lors de la création des utilisateurs : " . $e->getMessage());
            throw $e;
        }
    }
}

