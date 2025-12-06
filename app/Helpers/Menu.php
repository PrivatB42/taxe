<?php

use App\Helpers\Constantes;
use App\PhpFx\Menu\XMenu;
use Illuminate\Support\Facades\Auth;

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

/**
 * Vérifie si l'utilisateur connecté a un rôle spécifique
 */
function x_has_role(string ...$roles): bool
{
    $user = Auth::guard('api')->user() ?? Auth::user();
    if (!$user) {
        return true; // Par défaut, afficher tout si pas connecté (pour le dev)
    }
    return in_array($user->type_compte, $roles);
}

/**
 * Vérifie si l'utilisateur est superviseur
 */
function x_is_superviseur(): bool
{
    return x_has_role(Constantes::COMPTE_SUPERVISEUR);
}

/**
 * Vérifie si l'utilisateur est gestionnaire
 */
function x_is_gestionnaire(): bool
{
    return x_has_role(Constantes::COMPTE_GESTIONNAIRE);
}

function x_menu()
{
    // Menu Tableau de bord - visible pour tous
    $dashboard = [
        'name' => 'Tableau de bord',
        'icon' => 'fas fa-tachometer-alt',
        'route' => '/',
        'condition' => true,
        'subMenu' => [],
        'active' => url_() === '/',
    ];

    /**
     * MENU GESTIONNAIRE : Gestion des contribuables uniquement
     */

    $contribuables = [
        'name' => 'Contribuables',
        'icon' => 'fas fa-users',
        'route' => route('contribuables.index'),
        'condition' => x_has_role(Constantes::COMPTE_GESTIONNAIRE, Constantes::COMPTE_SUPERVISEUR),
        'subMenu' => [],
        'active' => url_() === 'contribuables' || str_starts_with(url_(), 'contribuables'),
    ];

    /**
     * MENU SUPERVISEUR : Gestion des gestionnaires et suivi des activités
     */

    $gestionnaires = [
        'name' => 'Gestionnaires',
        'icon' => 'fas fa-user-tie',
        'route' => route('gestionnaires.index'),
        'condition' => x_is_superviseur(),
        'subMenu' => [],
        'active' => url_tab(1) === 'gestionnaires',
    ];

    $activitesLog = [
        'name' => 'Activités',
        'icon' => 'fas fa-history',
        'route' => route('activites-log.index'),
        'condition' => x_is_superviseur(),
        'subMenu' => [],
        'active' => url_tab(1) === 'activites-log',
    ];

    $supervision = [
        'name' => 'Supervision',
        'icon' => 'fas fa-eye',
        'route' => '#',
        'condition' => x_is_superviseur(),
        'subMenu' => [
            $gestionnaires,
            $activitesLog
        ],
        'active' => url_tab(0) === 'utilisateurs' || url_tab(0) === 'supervision',
    ];

    /**
     * MENU CONFIGURATION - Superviseur uniquement
     */

    $activites = [
        'name' => 'Activites',
        'icon' => 'fas fa-briefcase',
        'route' => route('activites.index'),
        'condition' => x_is_superviseur(),
        'subMenu' => [],
        'active' => url_tab(1) === 'activites',
    ];

    $taxes = [
        'name' => 'Taxes',
        'icon' => 'fas fa-percent',
        'route' => route('taxes.index'),
        'condition' => x_is_superviseur(),
        'subMenu' => [],
        'active' => url_tab(1) === 'taxes',
    ];

    $active_taxe = [
        'name' => 'Activites Taxes',
        'icon' => 'fas fa-link',
        'route' => route('activites-taxes.index'),
        'condition' => x_is_superviseur(),
        'subMenu' => [],
        'active' => url_tab(1) === 'activites-taxes',
    ];

    $taxe_constante = [
        'name' => 'Constantes Taxes',
        'icon' => 'fas fa-calculator',
        'route' => route('taxes-constantes.index'),
        'condition' => x_is_superviseur(),
        'subMenu' => [],
        'active' => url_tab(1) === 'taxes-constantes',
    ];

    $configurations = [
        'name' => 'Configurations',
        'icon' => 'fas fa-cog',
        'route' => '#',
        'condition' => x_is_superviseur(),
        'subMenu' => [
            $activites,
            $taxes,
            $active_taxe,
            $taxe_constante
        ],
        'active' => url_tab(0) === 'configurations',
    ];

    // Construction du menu final selon le rôle
    $items = [
        $dashboard,
        $contribuables,      // Gestionnaire + Superviseur
        $supervision,        // Superviseur uniquement
        $configurations      // Superviseur uniquement
    ];

    return XMenu::get($items);
}


function x_make_menu(array $data)
{
    return XMenu::get($data);
}
