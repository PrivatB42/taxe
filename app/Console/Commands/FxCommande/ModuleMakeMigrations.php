<?php

namespace App\Console\Commands\FxCommande;

use Illuminate\Console\Command;

class ModuleMakeMigrations extends Command
{
    protected $signature = 'fxmodule:make-migrations {module} {migrations*}';
    protected $description = 'Créer plusieurs migrations dans un module';

    public function handle()
    {
        $module = $this->argument('module');
        $migrations = $this->argument('migrations');

        foreach ($migrations as $migration) {
            $this->call('module:make-migration', [
                'name' => 'create_'. $migration . '_table',
                'module' => $module,
            ]);
        }
    }
}
