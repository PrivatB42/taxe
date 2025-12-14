<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

echo "🔍 Vérification de toutes les routes...\n\n";

$routes = Route::getRoutes();
$routeList = [];

foreach ($routes as $route) {
    $methods = implode('|', $route->methods());
    $uri = $route->uri();
    $name = $route->getName() ?? 'N/A';
    
    $routeList[] = [
        'method' => $methods,
        'uri' => $uri,
        'name' => $name,
    ];
}

// Trier par URI
usort($routeList, function($a, $b) {
    return strcmp($a['uri'], $b['uri']);
});

echo "📋 Liste de toutes les routes enregistrées :\n";
echo str_repeat("=", 80) . "\n";
printf("%-8s %-40s %-30s\n", "METHOD", "URI", "NAME");
echo str_repeat("-", 80) . "\n";

foreach ($routeList as $route) {
    printf("%-8s %-40s %-30s\n", 
        $route['method'], 
        substr($route['uri'], 0, 40),
        substr($route['name'], 0, 30)
    );
}

echo "\n✅ Total : " . count($routeList) . " routes enregistrées\n\n";

// Vérifier les routes importantes
$importantRoutes = [
    'GET /' => 'home',
    'GET /dashboard' => 'dashboard',
    'GET /login' => 'login',
    'GET /auth/login' => 'auth.login',
    'POST /auth/connexion' => 'auth.connexion',
    'POST /auth/logout' => 'auth.logout',
];

echo "🔑 Vérification des routes importantes :\n";
echo str_repeat("=", 80) . "\n";

foreach ($importantRoutes as $routePattern => $expectedName) {
    $found = false;
    foreach ($routeList as $route) {
        if (strpos($route['method'], explode(' ', $routePattern)[0]) !== false && 
            $route['uri'] === explode(' ', $routePattern)[1]) {
            $found = true;
            $status = $route['name'] === $expectedName ? '✅' : '⚠️';
            echo sprintf("%s %-30s → %s (nom: %s)\n", 
                $status, 
                $routePattern,
                $route['uri'],
                $route['name']
            );
            break;
        }
    }
    if (!$found) {
        echo sprintf("❌ %-30s → ROUTE NON TROUVÉE\n", $routePattern);
    }
}

echo "\n";

