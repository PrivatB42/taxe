<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Database\Seeders\PermissionSeeder;
use Modules\User\Database\Seeders\TestUsersSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('👤 Initialisation des rôles...');
        $this->call(\Modules\User\Database\Seeders\RoleSeeder::class);
        
        $this->command->info('🌱 Initialisation des permissions...');
        $this->call(\Modules\User\Database\Seeders\PermissionSeeder::class);
        
        $this->command->info('👥 Création des utilisateurs de test...');
        $this->call(\Modules\User\Database\Seeders\TestUsersSeeder::class);
        
        $this->command->info('✅ Base de données initialisée avec succès !');
    }
}

