<?php

use App\Helpers\Constantes;
use App\PhpFx\Menu\XMenu;
use Illuminate\Support\Facades\Route;



/**exemple */
/**
 * [
 *    'name' => 'Tableau de bord',
 *    'icon' => 'fas fa-tachometer-alt',
 *    'route' => '#',
 *    'subMenu' => [],
 *    'condition' => true,
 *    'badge' => null,
 *    'active' => false,
 * ],
 */

function x_menu()
{
    $routeOr = function (string $name, string $default = '#') {
        return Route::has($name) ? route($name) : $default;
    };

    $dashboard = [
        'name' => 'Tableau de bord',
        'icon' => 'fas fa-tachometer-alt',
        'route' => '/',
        'condition' => true,
        'subMenu' => [],
        'active' => url_() === '/',
    ];

    /**
     * caisse (accès caissier)
     */

    $caisse = [
        'name' => 'Caisse',
        'icon' => 'fas fa-hand-holding-dollar',
        'route' => $routeOr('contribuables.index'),
        'condition' => can_permission(Constantes::PERMISSION_GERER_CAISSES)
            || can_permission(Constantes::PERMISSION_OUVRIR_FERMER_CAISSE)
            || can_permission(Constantes::PERMISSION_IMPRIMER_RECU),
        'subMenu' => [],
        'active' => url_tab(0) === 'contribuables',
    ];

    /**
     * utilisateur
     */

    $gestionnaires = [
        'name' => 'Utilisateurs',
        'icon' => 'fas fa-users',
        'route' => $routeOr('gestionnaires.index'),
        'condition' => can_permission(Constantes::PERMISSION_GERER_UTILISATEURS),
        'subMenu' => [],
        'active' => url_tab(1) === 'gestionnaires',
    ];

$roles = [
    'name' => 'Rôles',
    'icon' => 'fas fa-user-tag',
        'route' => $routeOr('roles.index'),
        'condition' => can_permission(Constantes::PERMISSION_GERER_ROLES),
    'subMenu' => [],
    'active' => url_tab(1) === 'roles',
];

$permissions = [
    'name' => 'Permissions',
    'icon' => 'fas fa-shield-alt',
        'route' => $routeOr('permissions.index'),
        'condition' => can_permission(Constantes::PERMISSION_GERER_PERMISSIONS),
    'subMenu' => [],
    'active' => url_tab(0) === 'permissions' || url_tab(1) === 'permissions',
];

    $utilisateurs = [
        'name' => 'Utilisateurs',
        'icon' => 'fas fa-users-cog',
        'route' => '#',
        'condition' => true,
        'subMenu' => [
        $gestionnaires,
        $roles,
        $permissions
        ],
        'active' => url_tab(0) === 'utilisateurs',
    ];

    /**
     * configuration
     */

    $activites = [
        'name' => 'Activites',
        'icon' => 'fas fa-briefcase',
        'route' => $routeOr('activites.index'),
        'condition' => can_permission(Constantes::PERMISSION_GERER_TAXES),
        'subMenu' => [],
        'active' => url_tab(1) === 'activites',
    ];

    $taxes = [
        'name' => 'Taxes',
        'icon' => 'fas fa-percent',
        'route' => $routeOr('taxes.index'),
        'condition' => can_permission(Constantes::PERMISSION_GERER_TAXES),
        'subMenu' => [],
        'active' => url_tab(1) === 'taxes',
    ];

    $active_taxe = [
        'name' => 'Activites Taxes',
        'icon' => 'fas fa-link',
        'route' => $routeOr('activites-taxes.index'),
        'condition' => can_permission(Constantes::PERMISSION_GERER_TAXES),
        'subMenu' => [],
        'active' => url_tab(1) === 'activites-taxes',
    ];

    $taxe_constante = [
        'name' => 'Constantes Taxes',
        'icon' => 'fas fa-c',
        'route' => $routeOr('taxes-constantes.index'),
        'condition' => can_permission(Constantes::PERMISSION_GERER_TAXES),
        'subMenu' => [],
        'active' => url_tab(1) === 'taxes-constantes',
    ];

    $configurations = [
        'name' => 'Configurations',
        'icon' => 'fas fa-cog',
        'route' => '#',
        'condition' => true,
        'subMenu' => [
            $activites,
            $taxes,
            $active_taxe,
            $taxe_constante
        ],
        'active' => url_tab(0) === 'configurations',
    ];

    //caisse (gestion)

    $caisseGestion = [
        'name' => 'Gérer caisse',
        'icon' => 'fas fa-cash-register',
        'route' => $routeOr('caisses.index'),
        // Visible uniquement pour admin (géré par can_permission) ou détenteurs des perms de création
        'condition' => can_permission(Constantes::PERMISSION_CREER_CAISSES)
            || can_permission(Constantes::PERMISSION_CREER_CAISSIERS),
        'subMenu' => [],
        'active' => url_tab(0) === 'caisses',
    ];

    $paiements = [
        'name' => 'Paiements',
        'icon' => 'fas fa-money-bill-wave',
        'route' => $routeOr('paiements.index'),
        'condition' => can_permission(Constantes::PERMISSION_ENCASSER),
        'subMenu' => [],
        'active' => url_tab(0) === 'paiements',
    ];


    $items = [
        $dashboard,
        $caisse,
        $utilisateurs,
        $configurations,
        $caisseGestion,
        $paiements
    ];

    return XMenu::get($items);
}


function x_make_menu(array $data)
{
    return XMenu::get($data);
}
