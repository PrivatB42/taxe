<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "🔍 Vérification de la structure des tables...\n\n";

// Vérifier user_roles
if (Schema::hasTable('user_roles')) {
    echo "✅ Table user_roles existe\n";
    $count = DB::table('user_roles')->count();
    echo "   Nombre de rôles : {$count}\n";
} else {
    echo "❌ Table user_roles n'existe pas\n";
}

// Vérifier user_permissions
if (Schema::hasTable('user_permissions')) {
    echo "✅ Table user_permissions existe\n";
    $count = DB::table('user_permissions')->count();
    echo "   Nombre de permissions : {$count}\n";
} else {
    echo "❌ Table user_permissions n'existe pas\n";
}

// Vérifier user_role_permissions
if (Schema::hasTable('user_role_permissions')) {
    echo "✅ Table user_role_permissions existe\n";
    
    // Vérifier les colonnes
    $columns = Schema::getColumnListing('user_role_permissions');
    echo "   Colonnes : " . implode(', ', $columns) . "\n";
    
    if (in_array('role_id', $columns)) {
        echo "   ✅ Colonne role_id existe (utilise les IDs)\n";
    } elseif (in_array('role', $columns)) {
        echo "   ⚠️  Colonne role existe (ancienne structure avec codes)\n";
        echo "   💡 Exécutez la migration 2025_01_20_000004 pour mettre à jour\n";
    }
    
    $count = DB::table('user_role_permissions')->count();
    echo "   Nombre d'attributions : {$count}\n";
} else {
    echo "❌ Table user_role_permissions n'existe pas\n";
}

echo "\n✅ Vérification terminée\n";

