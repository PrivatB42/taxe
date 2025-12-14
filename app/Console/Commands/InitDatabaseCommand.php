<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\User\Database\Seeders\PermissionSeeder;
use Modules\User\Database\Seeders\RoleSeeder;
use Modules\User\Database\Seeders\TestUsersSeeder;

class InitDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialise la base de données avec les migrations, permissions et utilisateurs de test';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Initialisation de la base de données...');
        $this->newLine();

        // Exécuter les migrations (si nécessaire)
        $this->info('📦 Vérification des migrations...');
        try {
            $this->call('migrate', ['--force' => true]);
            $this->info('✅ Migrations à jour');
        } catch (\Exception $e) {
            // Si les migrations sont déjà exécutées, on continue
            $this->warn('⚠️  Certaines migrations sont déjà exécutées, continuation...');
        }
        $this->newLine();

        // Exécuter le seeder de rôles
        $this->info('👤 Initialisation des rôles...');
        try {
            $seeder = new RoleSeeder();
            $seeder->setCommand($this);
            $seeder->run();
            $this->info('✅ Rôles initialisés');
            $this->newLine();
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'initialisation des rôles: ' . $e->getMessage());
            return 1;
        }

        // Exécuter le seeder de permissions
        $this->info('📋 Initialisation des permissions...');
        try {
            $seeder = new PermissionSeeder();
            $seeder->setCommand($this);
            $seeder->run();
            $this->info('✅ Permissions initialisées');
            $this->newLine();
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'initialisation des permissions: ' . $e->getMessage());
            return 1;
        }

        // Exécuter le seeder d'utilisateurs
        $this->info('👥 Création des utilisateurs de test...');
        try {
            $seeder = new TestUsersSeeder();
            $seeder->setCommand($this);
            $seeder->run();
            $this->info('✅ Utilisateurs créés');
            $this->newLine();
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la création des utilisateurs: ' . $e->getMessage());
            return 1;
        }

        $this->info('🎉 Initialisation terminée avec succès !');
        $this->newLine();
        $this->info('📧 Vous pouvez maintenant vous connecter avec :');
        $this->line('   - Email: admin@test.com');
        $this->line('   - Mot de passe: password123');

        return 0;
    }
}

