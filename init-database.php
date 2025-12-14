<?php

/**
 * Script d'initialisation de la base de données
 * 
 * Usage: php init-database.php
 * 
 * Ce script appelle simplement la commande Artisan db:init
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$exitCode = $kernel->call('db:init');

exit($exitCode);

