<?php

/**
 * Script de vérification des données dans la base de données
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Modules\User\Models\Permission;
use Modules\User\Models\RolePermission;
use Modules\User\Models\Personne;
use Modules\Auth\Models\Compte;
use Modules\User\Models\Gestionnaire;

$output = fopen('php://stdout', 'w') ?: fopen('php://output', 'w');

fwrite($output, "🔍 Vérification des données dans la base de données...\n\n");

// Vérifier les permissions
$permissions = Permission::count();
fwrite($output, "📋 Permissions : {$permissions}\n");
if ($permissions > 0) {
    Permission::all()->each(function($p) use ($output) {
        fwrite($output, "   - {$p->nom} ({$p->code})\n");
    });
} else {
    fwrite($output, "   ⚠️  Aucune permission trouvée\n");
}
fwrite($output, "\n");

// Vérifier les permissions par rôle
$rolePermissions = RolePermission::select('role', DB::raw('count(*) as count'))
    ->groupBy('role')
    ->get();
fwrite($output, "🔐 Permissions par rôle :\n");
if ($rolePermissions->count() > 0) {
    foreach ($rolePermissions as $rp) {
        fwrite($output, "   - {$rp->role} : {$rp->count} permission(s)\n");
    }
} else {
    fwrite($output, "   ⚠️  Aucune permission attribuée aux rôles\n");
}
fwrite($output, "\n");

// Vérifier les utilisateurs
$users = Personne::whereHas('gestionnaire')->with('gestionnaire', 'compte')->get();
fwrite($output, "👥 Utilisateurs : {$users->count()}\n");
if ($users->count() > 0) {
    foreach ($users as $user) {
        $status = $user->compte->is_active ? '✅ Actif' : '❌ Inactif';
        fwrite($output, "   - {$user->nom_complet} ({$user->email}) - Rôle: {$user->gestionnaire->role} - {$status}\n");
    }
} else {
    fwrite($output, "   ⚠️  Aucun utilisateur trouvé\n");
}
fwrite($output, "\n");

fwrite($output, "✅ Vérification terminée\n");

