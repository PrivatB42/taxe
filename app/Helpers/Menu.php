<?php

use App\Helpers\Constantes;
use App\PhpFx\Menu\XMenu;
use Illuminate\Support\Facades\Auth;

/**
 * Obtenir le type de compte de l'utilisateur connecté
 */
function get_user_role(): ?string
{
    return Auth::check() ? Auth::user()->type_compte : null;
}

/**
 * Vérifier si l'utilisateur peut voir un menu spécifique
 */
function can_see_menu(string $menuKey): bool
{
    $role = get_user_role();
    
    if (!$role) {
        return false;
    }

    // L'admin voit tout
    if ($role === Constantes::COMPTE_ADMIN) {
        return true;
    }

    return Constantes::canAccessMenu($role, $menuKey);
}

/**
 * Générer le menu selon le rôle de l'utilisateur
 */
function x_menu()
{
    $role = get_user_role();
    $items = [];

    // ====================================
    // DASHBOARD - Visible par tous sauf gestionnaire
    // ====================================
    if ($role !== Constantes::COMPTE_GESTIONNAIRE) {
        $items[] = [
            'name' => 'Tableau de bord',
            'icon' => 'fas fa-tachometer-alt',
            'route' => route('dashboard'),
            'condition' => true,
            'subMenu' => [],
            'active' => url_() === '/',
        ];
    }

    // ====================================
    // CONTRIBUABLES - Gestionnaire + Admin
    // ====================================
    if ($role === Constantes::COMPTE_GESTIONNAIRE || $role === Constantes::COMPTE_ADMIN) {
        $items[] = [
            'name' => 'Contribuables',
            'icon' => 'fas fa-users',
            'route' => route('contribuables.index'),
            'condition' => true,
            'subMenu' => [],
            'active' => url_() === 'contribuables' || str_starts_with(url_(), 'contribuables'),
        ];
    }

    // ====================================
    // SUPERVISION - Superviseur + Admin
    // ====================================
    if ($role === Constantes::COMPTE_SUPERVISEUR || $role === Constantes::COMPTE_ADMIN) {
        $gestionnaires = [
            'name' => 'Gestionnaires',
            'icon' => 'fas fa-user-tie',
            'route' => route('gestionnaires.index'),
            'condition' => true,
            'subMenu' => [],
            'active' => url_tab(1) === 'gestionnaires',
        ];

        $activitesLog = [
            'name' => 'Activités',
            'icon' => 'fas fa-history',
            'route' => route('activites-log.index'),
            'condition' => true,
            'subMenu' => [],
            'active' => url_tab(1) === 'activites-log',
        ];

        $items[] = [
            'name' => 'Supervision',
            'icon' => 'fas fa-eye',
            'route' => '#',
            'condition' => true,
            'subMenu' => [$gestionnaires, $activitesLog],
            'active' => url_tab(0) === 'supervision',
        ];
    }

    // ====================================
    // UTILISATEURS - Admin uniquement
    // ====================================
    if ($role === Constantes::COMPTE_ADMIN) {
        $gestionnairesAdmin = [
            'name' => 'Gestionnaires',
            'icon' => 'fas fa-users',
            'route' => route('gestionnaires.index'),
            'condition' => true,
            'subMenu' => [],
            'active' => url_tab(1) === 'gestionnaires',
        ];

        $items[] = [
            'name' => 'Utilisateurs',
            'icon' => 'fas fa-users-cog',
            'route' => '#',
            'condition' => true,
            'subMenu' => [$gestionnairesAdmin],
            'active' => url_tab(0) === 'utilisateurs',
        ];
    }

    // ====================================
    // CONFIGURATIONS - Admin uniquement
    // ====================================
    if ($role === Constantes::COMPTE_ADMIN) {
        $activites = [
            'name' => 'Activités',
            'icon' => 'fas fa-briefcase',
            'route' => route('activites.index'),
            'condition' => true,
            'subMenu' => [],
            'active' => url_tab(1) === 'activites',
        ];

        $taxes = [
            'name' => 'Taxes',
            'icon' => 'fas fa-percent',
            'route' => route('taxes.index'),
            'condition' => true,
            'subMenu' => [],
            'active' => url_tab(1) === 'taxes',
        ];

        $active_taxe = [
            'name' => 'Activités Taxes',
            'icon' => 'fas fa-link',
            'route' => route('activites-taxes.index'),
            'condition' => true,
            'subMenu' => [],
            'active' => url_tab(1) === 'activites-taxes',
        ];

        $taxe_constante = [
            'name' => 'Constantes Taxes',
            'icon' => 'fas fa-sliders-h',
            'route' => route('taxes-constantes.index'),
            'condition' => true,
            'subMenu' => [],
            'active' => url_tab(1) === 'taxes-constantes',
        ];

        $items[] = [
            'name' => 'Configurations',
            'icon' => 'fas fa-cog',
            'route' => '#',
            'condition' => true,
            'subMenu' => [$activites, $taxes, $active_taxe, $taxe_constante],
            'active' => url_tab(0) === 'configurations',
        ];

        // Caisses
        $items[] = [
            'name' => 'Caisses',
            'icon' => 'fas fa-cash-register',
            'route' => route('caisses.index'),
            'condition' => true,
            'subMenu' => [],
            'active' => url_tab(0) === 'caisses',
        ];
    }

    return XMenu::get($items);
}

function x_make_menu(array $data)
{
    return XMenu::get($data);
}
