<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ServeOnPortCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lancement du serveur sur le port 8500';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $port = env('APP_PORT', 8600);

        $this->line('');
        $this->line('  ------------------------------------------------------- ');
        $this->line('  ------------------------------------------------------- ');
        $this->info("    Starting development ".env('APP_NAME', '')." server on port {$port}... ");
        $this->line('  ------------------------------------------------------- ');
        $this->line('  ------------------------------------------------------- ');
        $this->line('');

        $this->call('serve', ['--port' => $port]);
    }
}
