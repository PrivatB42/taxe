<?php

namespace App\Helpers;


class Constantes
{
    public const COMPTE_CONTRIBUABLE = 'contribuable';
    public const COMPTE_GESTIONNAIRE = 'gestionnaire';
    public const COMPTE_SUPERVISEUR = 'superviseur';
    public const COMPTE_ADMIN = 'admin';

    // Préfixes des numéros de compte
    public const PREFIX_CONTRIBUABLE = 'CTR';
    public const PREFIX_GESTIONNAIRE = 'GES';
    public const PREFIX_SUPERVISEUR = 'SUP';
    public const PREFIX_ADMIN = 'ADM';

    public const COMPTES_PREFIX = [
        self::COMPTE_CONTRIBUABLE => self::PREFIX_CONTRIBUABLE,
        self::COMPTE_GESTIONNAIRE => self::PREFIX_GESTIONNAIRE,
        self::COMPTE_SUPERVISEUR => self::PREFIX_SUPERVISEUR,
        self::COMPTE_ADMIN => self::PREFIX_ADMIN,
    ];

    // Labels des types de compte
    public const COMPTES_LABELS = [
        self::COMPTE_CONTRIBUABLE => 'Contribuable',
        self::COMPTE_GESTIONNAIRE => 'Gestionnaire',
        self::COMPTE_SUPERVISEUR => 'Superviseur',
        self::COMPTE_ADMIN => 'Administrateur',
    ];

    // Couleurs des badges par type de compte
    public const COMPTES_COLORS = [
        self::COMPTE_CONTRIBUABLE => 'info',
        self::COMPTE_GESTIONNAIRE => 'primary',
        self::COMPTE_SUPERVISEUR => 'warning',
        self::COMPTE_ADMIN => 'danger',
    ];

    // Permissions par rôle
    // Le Gestionnaire ne peut que gérer les contribuables
    // Le Superviseur gère les gestionnaires et voit leurs activités
    // L'Admin a tous les droits
    public const PERMISSIONS = [
        self::COMPTE_GESTIONNAIRE => [
            'contribuables.index',
            'contribuables.store',
            'contribuables.update',
            'contribuables.show',
            'contribuables.toggle-active',
            'contribuables-activites.*',
            'contribuables-parametres.*',
            'contribuables-taxes.*',
            'dashboard.index',
        ],
        self::COMPTE_SUPERVISEUR => [
            'gestionnaires.*',
            'activites-log.*',
            'dashboard.*',
        ],
        self::COMPTE_ADMIN => [
            '*', // Tous les droits
        ],
    ];

    // Menus accessibles par rôle
    public const MENUS_PAR_ROLE = [
        self::COMPTE_GESTIONNAIRE => ['dashboard', 'contribuables'],
        self::COMPTE_SUPERVISEUR => ['dashboard', 'supervision'],
        self::COMPTE_ADMIN => ['*'], // Tous les menus
    ];

    public const COMMUNE_ID = 1;

    // Statuts de paiement
    public const STATUT_PAYE = 'paye';
    public const STATUT_NON_PAYE = 'non_paye';
    public const STATUT_PARTIEL = 'partiel';

    //['int', 'decimal', 'string', 'bool']
    const TYPE_DECIMAL = 'decimal';
    const TYPE_INT = 'int';
    const TYPE_STRING = 'string';
    const TYPE_BOOL = 'bool';

    const TYPE_CONSTANTES_TAXE = [
        ['type' => self::TYPE_DECIMAL, 'libelle' => 'Décimal'],
        ['type' => self::TYPE_INT, 'libelle' => 'Entier'],
        ['type' => self::TYPE_STRING, 'libelle' => 'Carractére'],
        ['type' => self::TYPE_BOOL, 'libelle' => 'Booleen'],
    ];

    const MULTIPLICATEUR_TAXE = [
        ['label' => '1', 'value' => 1],
        ['label' => '2', 'value' => 2],
        ['label' => '3', 'value' => 3],
        ['label' => '4', 'value' => 4],
        ['label' => '6', 'value' => 6],
        ['label' => '12', 'value' => 12],
    ];

    /**messages d'erreurs */
    public const ERROR_MESSAGE_500 = 'Une erreur interne est survenue veuillez contacter l\'administrateur';


    public const VALIDATION_MESSAGES = [
        // Règles de base
        'accepted' => 'Le champ :attribute doit être accepté.',
        'accepted_if' => 'Le champ :attribute doit être accepté lorsque :other est :value.',
        'active_url' => 'Le champ :attribute doit être une URL valide.',
        'after' => 'Le champ :attribute doit être une date après le :date.',
        'after_or_equal' => 'Le champ :attribute doit être une date après ou égale au :date.',
        'alpha' => 'Le champ :attribute ne doit contenir que des lettres.',
        'alpha_dash' => 'Le champ :attribute ne doit contenir que des lettres, chiffres, tirets et underscores.',
        'alpha_num' => 'Le champ :attribute ne doit contenir que des lettres et chiffres.',
        'array' => 'Le champ :attribute doit être un tableau.',
        'ascii' => 'Le champ :attribute ne doit contenir que des caractères alphanumériques et symboles sur un octet.',
        'before' => 'Le champ :attribute doit être une date avant le :date.',
        'before_or_equal' => 'Le champ :attribute doit être une date avant ou égale au :date.',

        // Règles avec paramètres (formats multiples)
        'between' => [
            'array' => 'Le champ :attribute doit avoir entre :min et :max éléments.',
            'file' => 'Le champ :attribute doit être entre :min et :max kilo-octets.',
            'numeric' => 'Le champ :attribute doit être entre :min et :max.',
            'string' => 'Le champ :attribute doit être entre :min et :max caractères.',
        ],

        'boolean' => 'Le champ :attribute doit être vrai ou faux.',
        'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
        'current_password' => 'Le mot de passe est incorrect.',
        'date' => 'Le champ :attribute doit être une date valide.',
        'date_equals' => 'Le champ :attribute doit être une date égale au :date.',
        'date_format' => 'Le champ :attribute doit correspondre au format :format.',
        'decimal' => 'Le champ :attribute doit avoir :decimal décimales.',
        'declined' => 'Le champ :attribute doit être décliné.',
        'declined_if' => 'Le champ :attribute doit être décliné lorsque :other est :value.',
        'different' => 'Le champ :attribute et :other doivent être différents.',
        'digits' => 'Le champ :attribute doit avoir :digits chiffres.',
        'digits_between' => 'Le champ :attribute doit avoir entre :min et :max chiffres.',
        'dimensions' => 'Les dimensions de l\'image :attribute ne sont pas valides.',
        'distinct' => 'Le champ :attribute a une valeur en double.',
        'doesnt_start_with' => 'Le champ :attribute ne doit pas commencer par l\'un des suivants : :values.',
        'doesnt_end_with' => 'Le champ :attribute ne doit pas se terminer par l\'un des suivants : :values.',
        'email' => 'Le champ :attribute doit être une adresse email valide.',
        'ends_with' => 'Le champ :attribute doit se terminer par l\'un des suivants : :values.',
        'enum' => 'La valeur sélectionnée pour :attribute est invalide.',
        'exists' => 'La valeur sélectionnée pour :attribute est invalide.',
        'file' => 'Le champ :attribute doit être un fichier.',
        'filled' => 'Le champ :attribute doit avoir une valeur.',
        'image' => 'Le champ :attribute doit être une image.',
        'in' => 'La valeur sélectionnée pour :attribute est invalide.',
        'in_array' => 'Le champ :attribute doit exister dans :other.',
        'integer' => 'Le champ :attribute doit être un entier.',
        'ip' => 'Le champ :attribute doit être une adresse IP valide.',
        'ipv4' => 'Le champ :attribute doit être une adresse IPv4 valide.',
        'ipv6' => 'Le champ :attribute doit être une adresse IPv6 valide.',
        'json' => 'Le champ :attribute doit être une chaîne JSON valide.',
        'lowercase' => 'Le champ :attribute doit être en minuscules.',

        // Règles de taille max
        'max' => [
            'array' => 'Le champ :attribute ne doit pas avoir plus de :max éléments.',
            'file' => 'Le champ :attribute ne doit pas dépasser :max kilo-octets.',
            'numeric' => 'Le champ :attribute ne doit pas être supérieur à :max.',
            'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        ],

        'max_digits' => 'Le champ :attribute ne doit pas avoir plus de :max chiffres.',
        'mimes' => 'Le champ :attribute doit être un fichier de type : :values.',
        'mimetypes' => 'Le champ :attribute doit être un fichier de type : :values.',

        // Règles de taille min
        'min' => [
            'array' => 'Le champ :attribute doit avoir au moins :min éléments.',
            'file' => 'Le champ :attribute doit faire au moins :min kilo-octets.',
            'numeric' => 'Le champ :attribute doit être au moins :min.',
            'string' => 'Le champ :attribute doit avoir au moins :min caractères.',
        ],

        'min_digits' => 'Le champ :attribute doit avoir au moins :min chiffres.',
        'missing' => 'Le champ :attribute doit être manquant.',
        'missing_if' => 'Le champ :attribute doit être manquant lorsque :other est :value.',
        'missing_unless' => 'Le champ :attribute doit être manquant sauf si :other est :value.',
        'missing_with' => 'Le champ :attribute doit être manquant lorsque :values est présent.',
        'missing_with_all' => 'Le champ :attribute doit être manquant lorsque :values sont présents.',
        'multiple_of' => 'Le champ :attribute doit être un multiple de :value.',
        'not_in' => 'La valeur sélectionnée pour :attribute est invalide.',
        'not_regex' => 'Le format du champ :attribute est invalide.',
        'numeric' => 'Le champ :attribute doit être un nombre.',

        // Règles de mot de passe
        'password' => [
            'letters' => 'Le mot de passe doit contenir au moins une lettre.',
            'mixed' => 'Le mot de passe doit contenir au moins une majuscule et une minuscule.',
            'numbers' => 'Le mot de passe doit contenir au moins un chiffre.',
            'symbols' => 'Le mot de passe doit contenir au moins un symbole.',
            'uncompromised' => 'Ce mot de passe a été compromis. Veuillez en choisir un autre.',
        ],

        // Règles de présence
        'present' => 'Le champ :attribute doit être présent.',
        'prohibited' => 'Le champ :attribute est interdit.',
        'prohibited_if' => 'Le champ :attribute est interdit lorsque :other est :value.',
        'prohibited_unless' => 'Le champ :attribute est interdit sauf si :other est dans :values.',
        'prohibits' => 'Le champ :attribute interdit la présence de :other.',
        'regex' => 'Le format du champ :attribute est invalide.',
        'required' => 'Le champ :attribute est obligatoire.',
        'required_array_keys' => 'Le champ :attribute doit contenir des entrées pour : :values.',
        'required_if' => 'Le champ :attribute est obligatoire lorsque :other est :value.',
        'required_if_accepted' => 'Le champ :attribute est obligatoire lorsque :other est accepté.',
        'required_unless' => 'Le champ :attribute est obligatoire sauf si :other est dans :values.',
        'required_with' => 'Le champ :attribute est obligatoire lorsque :values est présent.',
        'required_with_all' => 'Le champ :attribute est obligatoire lorsque :values sont présents.',
        'required_without' => 'Le champ :attribute est obligatoire lorsque :values n\'est pas présent.',
        'required_without_all' => 'Le champ :attribute est obligatoire lorsqu\'aucun de :values n\'est présent.',

        // Règles de similitude
        'same' => 'Le champ :attribute et :other doivent correspondre.',

        // Règles de taille exacte
        'size' => [
            'array' => 'Le champ :attribute doit contenir :size éléments.',
            'file' => 'Le champ :attribute doit faire :size kilo-octets.',
            'numeric' => 'Le champ :attribute doit être :size.',
            'string' => 'Le champ :attribute doit faire :size caractères.',
        ],

        'starts_with' => 'Le champ :attribute doit commencer par l\'un des suivants : :values.',
        'string' => 'Le champ :attribute doit être une chaîne de caractères.',
        'timezone' => 'Le champ :attribute doit être un fuseau horaire valide.',
        'unique' => 'Cette valeur existe déjà pour le champ :attribute.',
        'uploaded' => 'Le téléchargement du fichier :attribute a échoué.',
        'uppercase' => 'Le champ :attribute doit être en majuscules.',
        'url' => 'Le champ :attribute doit être une URL valide.',
        'ulid' => 'Le champ :attribute doit être un ULID valide.',
        'uuid' => 'Le champ :attribute doit être un UUID valide.',

        // Règles conditionnelles
        'sometimes' => 'Le champ :attribute est parfois requis.',

        // Règles personnalisées (optionnelles)
        'phone' => 'Le champ :attribute doit être un numéro de téléphone valide.',
        'currency' => 'Le champ :attribute doit être une devise valide.',
        'domain' => 'Le champ :attribute doit être un domaine valide.',
        'mac_address' => 'Le champ :attribute doit être une adresse MAC valide.',
        'hex_color' => 'Le champ :attribute doit être une couleur hexadécimale valide.',
        'iban' => 'Le champ :attribute doit être un IBAN valide.',
        'credit_card' => 'Le champ :attribute doit être un numéro de carte de crédit valide.',
        'issn' => 'Le champ :attribute doit être un ISSN valide.',
        'isbn' => 'Le champ :attribute doit être un ISBN valide.',
    ];

    /**
     * Vérifier si un utilisateur a une permission
     */
    public static function hasPermission(string $typeCompte, string $permission): bool
    {
        // L'admin a tous les droits
        if ($typeCompte === self::COMPTE_ADMIN) {
            return true;
        }

        $permissions = self::PERMISSIONS[$typeCompte] ?? [];
        
        foreach ($permissions as $perm) {
            // Permission exacte
            if ($perm === $permission) {
                return true;
            }
            // Permission avec wildcard (ex: gestionnaires.* => gestionnaires.index)
            if (str_ends_with($perm, '.*')) {
                $prefix = str_replace('.*', '', $perm);
                if (str_starts_with($permission, $prefix)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Vérifier si un type de compte peut accéder à un menu
     */
    public static function canAccessMenu(string $typeCompte, string $menu): bool
    {
        // L'admin peut accéder à tout
        if ($typeCompte === self::COMPTE_ADMIN) {
            return true;
        }

        $menus = self::MENUS_PAR_ROLE[$typeCompte] ?? [];
        
        return in_array('*', $menus) || in_array($menu, $menus);
    }
}
