<?php

use App\Helpers\Constantes;
use App\PhpFx\Facades\Form\AdvanceForm;
use App\PhpFx\Facades\Form\XFormFacade;
use App\PhpFx\Form\FilePut;
use App\PhpFx\Form\FormFetch;
use App\PhpFx\Form\FormFx;
use App\PhpFx\Form\FormMultiStep;
use App\PhpFx\Form\XForm;
use App\PhpFx\Html\Html;
use App\PhpFx\Html\HtmlMultiplicate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Modules\Entite\Models\Taxe;
use Modules\User\Models\Contribuable;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * return une date dans un format souhaité
 * @param $date
 * @param string $format
 * @return date
 */
if (!function_exists('format_date')) {
    function format_date($date, string $format = 'd/m/Y')
    {
        return Carbon::parse($date)->format($format);
    }
}

if (!function_exists('parse_date')) {
    function parse_date($date, $timeZone = null)
    {
        return Carbon::parse($date, $timeZone);
    }
}

if (!function_exists('generate_password')) {
    if (!function_exists('generate_strong_password')) {
        /**
         * Génère un mot de passe fort de 8 caractères
         * contenant au moins une majuscule, une minuscule, 
         * un chiffre et un caractère spécial
         * 
         * @return string
         */
        function generate_strong_password(int $length = 8): string
        {
            $lowercase = 'abcdefghjkmnpqrstuvwxyz';
            $uppercase = 'ABCDEFGHJKMNPQRSTUVWXYZ';
            $numbers = '23456789';
            $specials = '!@#$%&*()-_=+';

            // Prend au moins un caractère de chaque catégorie
            $password = [
                $lowercase[random_int(0, strlen($lowercase) - 1)],
                $uppercase[random_int(0, strlen($uppercase) - 1)],
                $numbers[random_int(0, strlen($numbers) - 1)],
                $specials[random_int(0, strlen($specials) - 1)]
            ];

            // Remplit les 4 caractères restants avec un mélange
            $allChars = $lowercase . $uppercase . $numbers . $specials;
            for ($i = 4; $i < $length; $i++) {
                $password[] = $allChars[random_int(0, strlen($allChars) - 1)];
            }

            // Mélange le tableau et le convertit en string
            shuffle($password);
            return implode('', $password);
        }
    }
}

/**
 * genere un slug a partir d'un string ( genere slug => genere-slug)
 * @param string $string
 * @return string
 */
if (!function_exists('generate_slug')) {
    function generate_slug(string $string)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    }
}

if (!function_exists('format_validation_errors')) {
    function format_validation_errors($errors)
    {
        if (is_object($errors) && method_exists($errors, 'all')) {
            return implode("\n", $errors->all());
        }

        return 'Une erreur inconnue est survenue.';
    }
}



/**
 * Collection de helpers pour la manipulation de données (tableaux et objets)
 */

/**
 * Insère des données dans un tableau ou objet existant
 * 
 * @param array|object $table Tableau ou objet à modifier
 * @param array $data Données à insérer sous forme de tableau associatif
 * @return array|object La structure originale avec les nouvelles données ajoutées
 */
function insert_table(array|object $table, array $data)
{
    if (!exist($table) || !exist($data)) {
        return $table;
    }

    $data = (array)$data;
    foreach ($data as $key => $value) {
        $table[$key] = $value;
    }
    return $table;
}

/**
 * Supprime des clés spécifiques d'un tableau
 * 
 * @param array $data Tableau source
 * @param array $unsetData Liste des clés à supprimer
 * @return array Le tableau nettoyé
 */
function unset_data(array $data, array $unsetData)
{
    if (!exist($data) || !exist($unsetData)) {
        return [];
    }

    $datas = (array)$data;
    foreach ($unsetData as $value) {
        if (isset($datas[$value])) {
            unset($datas[$value]);
        }
    }
    return $datas;
}

/**
 * Supprime des propriétés spécifiques d'un objet
 * 
 * @param object $data Objet source
 * @param array $unsetData Liste des propriétés à supprimer
 * @return object L'objet nettoyé
 */
function unset_obj_data(object $data, array $unsetData)
{
    if (!exist($data) || !exist($unsetData)) {
        return new stdClass();
    }

    $datas = $data;
    foreach ($unsetData as $value) {
        if (isset($datas->$value)) {
            unset($datas->$value);
        }
    }
    return $datas;
}

/**
 * Conserve uniquement les propriétés spécifiées d'un objet
 * 
 * @param object $data Objet source
 * @param array $noUnsetData Liste des propriétés à conserver
 * @return object Nouvel objet avec seulement les propriétés demandées
 */
function reverse_unset_obj_data(object | Null $data, array $noUnsetData)
{
    if (!exist($data) || !exist($noUnsetData)) {
        return new stdClass();
    }

    $datas = new stdClass();
    foreach ($noUnsetData as $value) {
        if (isset($data->$value)) {
            $datas->$value = $data->$value;
        }
    }
    return $datas;
}

/**
 * Crée un objet avec les propriétés spécifiées initialisées à null
 * 
 * @param array $array Liste des propriétés à créer
 * @return object Nouvel objet avec les propriétés initialisées à null
 */
function object_items_null(array $array)
{
    if (!exist($array)) {
        return new stdClass();
    }

    $datas = new stdClass();
    foreach ($array as $value) {
        $datas->$value = null;
    }
    return $datas;
}

/**
 * Transforme les clés d'un tableau selon un mapping spécifié
 * 
 * @param array|object $data Données source
 * @param array $arrayMap Tableau de mapping [nouvelle_clé => ancienne_clé]
 * @return array Tableau avec les clés transformées
 */
function map_data(array|object $data, array $arrayMap)
{
    if (!exist($data) || !exist($arrayMap)) {
        return [];
    }

    $data = (array)$data;
    $datas = array();
    foreach ($arrayMap as $key => $value) {
        $key = (is_numeric($key)) ? $value : $key;
        if (isset($data[$value])) {
            $datas[$key] = $data[$value];
        }
    }
    return $datas;
}

/**
 * Filtre un tableau de règles selon les clés présentes dans les données
 * 
 * @param array|object $data Données source
 * @param array $arrayMap Tableau de règles complètes
 * @return array Tableau de règles filtré
 */
function map_data_rule(array|object $data, array $arrayMap)
{
    if (!exist($data) || !exist($arrayMap)) {
        return [];
    }

    $data = (array)$data;
    $rules = array();
    foreach ($data as $key => $value) {
        if (isset($arrayMap[$key])) {
            $rules[$key] = $arrayMap[$key];
        }
    }
    return $rules;
}

/**
 * Supprime les accents d'une chaîne de caractères
 * 
 * @param string $string Chaîne à traiter
 * @return string Chaîne sans accents
 */
function removeAccents($string)
{
    if (!exist($string)) {
        return '';
    }

    $accents = [
        'é' => 'e',
        'è' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'à' => 'a',
        'â' => 'a',
        'ä' => 'a',
        'ô' => 'o',
        'ö' => 'o',
        'ò' => 'o',
        'ù' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ç' => 'c',
        'î' => 'i',
        'ï' => 'i',
        'ñ' => 'n'
    ];

    return strtr($string, $accents);
}

/**
 * Vérifie si une variable existe et n'est pas vide
 * 
 * @param mixed $var Variable à vérifier
 * @return bool True si la variable existe et n'est pas vide
 */
function exist($var): bool
{
    if (!isset($var)) {
        return false;
    }

    if (is_string($var) && trim($var) === '') {
        return false;
    }

    if (is_array($var) && empty($var)) {
        return false;
    }

    if ($var instanceof Countable && count($var) === 0) {
        return false;
    }

    return true;
}


function url_()
{
    return request()->path();
}

function nombreToText($nombre)
{
    $formatter = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
    return $formatter->format($nombre);
}


function url_tab($key = '')
{
    if ($key == '') {
        return explode('/', request()->path()) ?? [];
    }

    $current = explode('/', request()->path())[$key] ?? '';

    return $current == '' ? '/' : $current;
}

function url_last()
{
    return explode('/', request()->path())[count(explode('/', request()->path())) - 1] ?? '';
}

if (!function_exists('xForm')) {
    function xForm()
    {
        return XFormFacade::self();
    }
}

if (!function_exists('xFormBuilder')) {
    function xFormBuilder(bool $useBootstrap = true)
    {
        return new XForm($useBootstrap);
    }
}


if (!function_exists('formFxBuilder')) {
    function formFxBuilder(bool $useBootstrap = true)
    {
        return new FormFx($useBootstrap);
    }
}

if (!function_exists('advanceForm')) {
    function advanceForm(array $config = [])
    {
        $form = new AdvanceForm();
        echo $form::assets($config);
        return $form;
    }
}

if (!function_exists('formFetch')) {
    function formFetch()
    {
        return new FormFetch();
    }
}

if (!function_exists('xhtml')) {
    function xhtml()
    {
        return new Html();
    }
}

if (!function_exists('formmultiStep')) {
    function formmultiStep(string $formId, array $config = [])
    {
        return new FormMultiStep($formId, $config);
    }
}


if (!function_exists('htmlMultiplicate')) {
    function htmlMultiplicate()
    {
        return new HtmlMultiplicate();
    }
}


if (!function_exists('filePut')) {
    function filePut($css = true)
    {
        $filePut = new FilePut();
        if ($css) {
            $filePut->pushCss();
        }
        return $filePut;
    }
}






if (!function_exists('globalSearch')) {

    /**
     * Ajoute des conditions de recherche globale à une requête Eloquent
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $globalSearch
     * @param array $searchFields Tableau des champs à rechercher (format: ['nom', 'pays.nom', 'user.profile.address', etc.])
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function globalSearch($query, string $globalSearch, array $searchFields)
    {
        if (empty($globalSearch) || empty($searchFields)) {
            return $query;
        }

        $query->where(function ($q) use ($globalSearch, $searchFields) {
            $isFirstCondition = true;

            foreach ($searchFields as $field) {
                // Vérifie si le champ contient une relation (syntaxe avec points)
                if (str_contains($field, '.')) {
                    $fieldParts = explode('.', $field);
                    $fieldName = array_pop($fieldParts); // Le dernier élément est le nom du champ
                    $relations = $fieldParts; // Les autres éléments forment le chemin de relation

                    $whereMethod = $isFirstCondition ? 'whereHas' : 'orWhereHas';

                    // Construire la requête de relation récursive
                    $q->{$whereMethod}($relations[0], function ($subQuery) use ($relations, $fieldName, $globalSearch, $field) {
                        buildNestedRelationQuery($subQuery, $relations, 1, $fieldName, $globalSearch);
                    });
                } else {
                    $whereMethod = $isFirstCondition ? 'where' : 'orWhere';
                    $q->{$whereMethod}($field, 'like', "%{$globalSearch}%");
                }

                $isFirstCondition = false;
            }
        });

        return $query;
    }

    /**
     * Construit récursivement les requêtes de relations imbriquées
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $relations
     * @param int $currentIndex
     * @param string $fieldName
     * @param string $searchTerm
     * @return void
     */
    function buildNestedRelationQuery($query, array $relations, int $currentIndex, string $fieldName, string $searchTerm)
    {
        // Si on est au dernier niveau de relation
        if ($currentIndex >= count($relations)) {
            $query->where($fieldName, 'like', "%{$searchTerm}%");
            return;
        }

        // Sinon, continuer à creuser dans les relations
        $currentRelation = $relations[$currentIndex];

        $query->whereHas($currentRelation, function ($subQuery) use ($relations, $currentIndex, $fieldName, $searchTerm) {
            buildNestedRelationQuery($subQuery, $relations, $currentIndex + 1, $fieldName, $searchTerm);
        });
    }
}


if (!function_exists('dataTableSorting')) {
    /**
     * Applique un tri à la requête en fonction des paramètres DataTables
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @param array $sortableColumns Liste des colonnes triables
     * @param array $columnMap Mapping optionnel entre les noms de colonnes frontend et backend
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function dataTablesSorting($query, Request $request, array $sortableColumns, array $columnMap = [])
    {
        if (!$request->has('order')) {
            return $query;
        }

        $order = $request->input('order')[0];
        $columnIndex = $order['column'];
        $frontendColumnName = $request->input("columns.{$columnIndex}.data");
        $direction = $order['dir'];

        // Utilise le mapping si fourni, sinon utilise le nom tel quel
        $backendColumnName = $columnMap[$frontendColumnName] ?? $frontendColumnName;

        if (in_array($backendColumnName, $sortableColumns)) {
            // Gestion des relations si le champ contient un point (relation.champ)
            if (str_contains($backendColumnName, '.')) {
                [$relation, $relationColumn] = explode('.', $backendColumnName, 2);
                $query->with([$relation => function ($q) use ($relationColumn, $direction) {
                    $q->orderBy($relationColumn, $direction);
                }]);
            } else {
                $query->orderBy($backendColumnName, $direction);
            }
        }

        return $query;
    }
}

if (!function_exists('dataTablePaginate')) {
    /**
     * Automatise la pagination et le tri pour les DataTables avec support multi-colonne
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @param array $options [
     *     'columns' => array,           // Colonnes disponibles pour le tri
     *     'defaultOrder' => array|array[], // Ordre par défaut (simple ou multiple)
     *     'returnArray' => bool,        // Retourne un tableau si true
     * ]
     * @return array|\Illuminate\Http\JsonResponse
     */
    function datatablePaginate($query, Request $request, array $options = [])
    {
        // Options par défaut
        $defaults = [
            'columns' => [],
            'defaultOrder' => [['column' => 'id', 'direction' => 'desc']],
            'returnArray' => true,
        ];

        $options = array_merge($defaults, $options);

        // Pagination
        $total = $query->count();
        $start = intval($request->input('start', 0));
        $length = intval($request->input('length', 10));

        // Gestion des tris (multi-colonne)
        $orders = [];

        // 1. Tri depuis la requête DataTables
        if ($request->has('order')) {
            foreach ($request->input('order') as $order) {
                $colIndex = $order['column'];
                if (isset($options['columns'][$colIndex])) {
                    $column = $options['columns'][$colIndex];
                    $orders[] = [
                        'column' => $column['name'] ?? $column['data'] ?? $colIndex,
                        'direction' => $order['dir'] ?? 'asc',
                    ];
                }
            }
        }

        // 2. Fallback sur le tri par défaut si aucun tri spécifié
        if (empty($orders)) {
            $orders = is_array(reset($options['defaultOrder']))
                ? $options['defaultOrder']
                : [$options['defaultOrder']];
        }

        // Application des tris
        foreach ($orders as $order) {
            $query->orderBy($order['column'], $order['direction'] ?? 'asc');
        }

        // Récupération des données
        $data = $query
            ->offset($start)
            ->limit($length)
            ->get();

        $result = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ];

        return $options['returnArray'] ? $result : response()->json($result);
    }
}

if (!function_exists('apply_filters')) {
    /**
     * Applique des filtres à une requête Eloquent avec support des relations
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @param array $filterableFields Liste des champs filtrables
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function apply_filters($query, $request, array $filterableFields = [])
    {
        if (!$request->has('filters') || !is_array($request->input('filters'))) {
            return $query;
        }

        $query->where(function ($q) use ($request, $filterableFields) {
            foreach ($request->input('filters') as $filter) {
                if (
                    !isset($filter['name'], $filter['value']) ||
                    (count($filterableFields) > 0 && !in_array($filter['name'], $filterableFields))
                ) {
                    continue;
                }

                $operator = $filter['operator'] ?? 'like';
                $value = $operator === 'like' ? "%{$filter['value']}%" : $filter['value'];

                // Si le champ contient une relation (syntaxe avec point)
                if (str_contains($filter['name'], '.')) {
                    [$relation, $relationField] = explode('.', $filter['name'], 2);

                    $q->whereHas($relation, function ($subQuery) use ($relationField, $operator, $value) {
                        $subQuery->where($relationField, $operator, $value);
                    });
                } else {
                    $q->where($filter['name'], $operator, $value);
                }
            }
        });

        return $query;
    }
}


if (!function_exists('apply_column_filters')) {
    /**
     * Applique les filtres par colonnes avec support des relations
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @param array $searchableColumns Liste des colonnes filtrables (optionnel)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function apply_column_filters($query, $request, array $searchableColumns = [], array $colmnsDefaultFilters = [])
    {
        $columns = $request->get('columns', $colmnsDefaultFilters);

        if (empty($columns)) {
            return $query;
        }

        $query->where(function ($q) use ($columns, $searchableColumns) {
            foreach ($columns as $col) {
                $colName = $col['name'] ?? $col['data'] ?? null;
                $colValue = $col['search']['value'] ?? null;

                // Vérifie si la colonne doit être filtrée
                if (
                    !$colName || !$colValue ||
                    (!empty($searchableColumns) && !in_array($colName, $searchableColumns))
                ) {
                    continue;
                }

                // Gestion des relations (syntaxe avec point)
                if (str_contains($colName, '.')) {
                    [$relation, $relationField] = explode('.', $colName, 2);

                    $q->whereHas($relation, function ($subQuery) use ($relationField, $colValue) {
                        $subQuery->where($relationField, 'like', "%$colValue%");
                    });
                } else {
                    $q->where($colName, 'like', "%$colValue%");
                }
            }
        });

        return $query;
    }
}


if (!function_exists('applyAdvancedColumnFilters')) {
    /**
     * Applique les filtres par colonnes avec options avancées
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @param array $options [
     *     'searchable_columns' => [], // Colonnes autorisées
     *     'column_map' => [], // Mapping des noms de colonnes
     *     'exact_match_columns' => [], // Colonnes avec recherche exacte
     *     'date_columns' => [], // Colonnes de date avec traitement spécial
     * ]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function applyAdvancedColumnFilters($query, $request, array $options = [])
    {
        $defaultOptions = [
            'searchable_columns' => [],
            'column_map' => [],
            'exact_match_columns' => [],
            'date_columns' => [],
        ];
        $options = array_merge($defaultOptions, $options);

        $columns = $request->get('columns', []);

        if (empty($columns)) {
            return $query;
        }

        $query->where(function ($q) use ($columns, $options) {
            foreach ($columns as $col) {
                $originalColName = $col['name'] ?? $col['data'] ?? null;
                $colValue = $col['search']['value'] ?? null;

                if (!$originalColName || !$colValue) {
                    continue;
                }

                // Applique le mapping des noms de colonnes
                $colName = $options['column_map'][$originalColName] ?? $originalColName;

                // Vérifie si la colonne est filtrable
                if (
                    !empty($options['searchable_columns']) &&
                    !in_array($colName, $options['searchable_columns'])
                ) {
                    continue;
                }

                // Détermine l'opérateur en fonction du type de colonne
                $operator = 'like';
                $value = "%$colValue%";

                if (in_array($colName, $options['exact_match_columns'])) {
                    $operator = '=';
                    $value = $colValue;
                } elseif (in_array($colName, $options['date_columns'])) {
                    // Traitement spécial pour les dates
                    try {
                        $date = Carbon::parse($colValue)->startOfDay();
                        $q->whereDate($colName, '=', $date);
                        continue;
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                // Gestion des relations
                if (str_contains($colName, '.')) {
                    [$relation, $relationField] = explode('.', $colName, 2);

                    $q->whereHas($relation, function ($subQuery) use ($relationField, $operator, $value) {
                        $subQuery->where($relationField, $operator, $value);
                    });
                } else {
                    $q->where($colName, $operator, $value);
                }
            }
        });

        return $query;
    }
}


function processDataTable(Request $request, $query, $options = [], $returnType = 'json')
{
    // Options par défaut
    $defaultOptions = [
        'searchable_columns' => [], // Colonnes pour la recherche globale
        'filterable_columns' => [], // Colonnes pour les filtres
        'sortable_columns' => [],   // Colonnes triables
        'relations' => [],          // Relations disponibles
        'default_order' => ['column' => 'id', 'dir' => 'desc'],
        'length_default' => 10,
        'resource' => null,         // Resource pour la transformation des données
    ];

    $options = array_merge($defaultOptions, $options);

    // 1. 🔍 Recherche globale
    if ($globalSearch = $request->input('search.value')) {

        $query = globalSearch($query, $globalSearch, $options['searchable_columns']);
    }

    // 2. 🎛️ Filtres personnalisés
    if ($request->input('filters') && count($request->input('filters')) > 0) {
        foreach ($request->input('filters') as $filter) {
            if (
                isset($filter['name'], $filter['value']) &&
                (empty($options['filterable_columns']) || in_array($filter['name'], $options['filterable_columns']))
            ) {
                applyFilterCondition($query, $filter['name'], $filter['value'], $filter['operator'] ?? 'like');
            }
        }
    }

    // 3. 🔍 Filtres par colonnes
    $columns = $request->get('columns', []);
    foreach ($columns as $col) {
        $colName = $col['name'] ?? $col['data'] ?? null;
        $colValue = $col['search']['value'] ?? null;

        if ($colName && $colValue && (empty($options['filterable_columns']) || in_array($colName, $options['filterable_columns']))) {
            applyFilterCondition($query, $colName, $colValue);
        }
    }


    // 4. ⬆️ Tri
    if ($request->has('order')) {
        $order = $request->input('order')[0];
        $columnIndex = $order['column'];
        $columnName = $request->input("columns.{$columnIndex}.data");

        if (in_array($columnName, $options['sortable_columns'])) {
            $direction = $order['dir'];
            if (count($options['relations']) > 0) {
                $options['relations'] = generateRelationConfig($query->getModel(), $options['relations']);
            }
            applyOrderBy($query, $columnName, $direction, $options);
        }
    }

    // 5. 📊 Pagination
    $total = $query->count();
    $start = intval($request->input('start', 0));
    $length = intval($request->input('length', $options['length_default']));

    // Tri par défaut si aucun tri spécifié
    if (/*!$request->has('order')*/count($options['default_order']) > 0) {

        if (count($options['relations']) > 0) {
            $options['default_order']['column'] = $query->getModel()->getTable() . '.' . $options['default_order']['column'];
        }
        applyOrderBy($query, $options['default_order']['column'], $options['default_order']['dir'], $options);
    }


    $data = $query->offset($start)->limit($length)->get();

    $result = [
        'draw' => intval($request->input('draw')),
        'recordsTotal' => $total,
        'recordsFiltered' => $total,
        'data' => $options['resource'] ? $options['resource']::collection($data) : $data,
    ];

    return $returnType === 'json' ? response()->json($result) : $result;
}

// Helper pour appliquer les conditions de filtre avec relations
function applyFilterCondition($query, $field, $value, $operator = 'like')
{
    $value = $operator === 'like' ? "%$value%" : $value;

    if (str_contains($field, '.')) {
        $relations = explode('.', $field);
        $column = array_pop($relations);

        $query->whereHas(implode('.', $relations), function ($q) use ($column, $operator, $value) {
            $q->where($column, $operator, $value);
        });
    } else {
        $query->where($field, $operator, $value);
    }
}

// Helper pour appliquer le tri avec relations
function applyOrderBy($query, $column, $direction, $options)
{
    if (str_contains($column, '.')) {
        $relations = explode('.', $column);
        $column = array_pop($relations);

        // Pour les relations, on utilise un join plutôt qu'un whereHas pour le tri
        $relationPath = '';
        $previousTable = $query->getModel()->getTable();

        foreach ($relations as $relation) {
            $relationPath = $relationPath ? "$relationPath.$relation" : $relation;
            $relatedModel = $options['relations'][$relationPath] ?? null;

            if ($relatedModel) {
                $query->join(
                    $relatedModel['table'],
                    "$previousTable.{$relatedModel['foreign_key']}",
                    '=',
                    "{$relatedModel['table']}.{$relatedModel['local_key']}"
                );
                $previousTable = $relatedModel['table'];
            }
        }

        $query->orderBy("$previousTable.$column", $direction);
    } else {
        $query->orderBy($column, $direction);
    }
}

function generateRelationConfig($model, $relations)
{
    $config = [];

    foreach ($relations as $relationPath) {
        $parts = explode('.', $relationPath);
        $currentModel = $model;
        $fullPath = '';

        foreach ($parts as $part) {
            $fullPath = $fullPath ? "$fullPath.$part" : $part;

            $relatedModel = $currentModel->$part()->getRelated();
            $foreignKey = $currentModel->$part()->getForeignKeyName();
            $localKey = $currentModel->$part()->getOwnerKeyName();

            $config[$fullPath] = [
                'table' => $relatedModel->getTable(),
                'foreign_key' => $foreignKey,
                'local_key' => $localKey
            ];

            $currentModel = $relatedModel;
        }
    }

    return $config;
}


if (!function_exists('button_modal')) {
    function button_modal($name = null, $class = null, $icon = null, $modalId = null)
    {
        return view('components.generic.c-modal.button-modal', compact('name', 'class', 'icon', 'modalId'));
    }
}


if (!function_exists('getSringAfterSegment')) {
    function getSringAfterSegment($url, $search)
    {

        $position = strpos($url, $search);
        if ($position !== false) {
            return substr($url, $position + strlen($search));
        }

        return '';
    }


    if (!function_exists('tagRequired')) {
        function tagRequired()
        {
            return '<span class="text-danger">*</span>';
        }
    }
}


if (!function_exists('arrayFusion')) {
    function arrayFusion($cles, $valeurs)
    {
        $resultat = array();

        foreach ($cles as $index => $cle) {
            // Vérifier si l'index existe dans le tableau des valeurs
            if (!array_key_exists($index, $valeurs)) {
                continue;
            }

            $valeur = $valeurs[$index];

            // Ignorer si la clé est null OU si la valeur est null
            if ($cle === null || $valeur === null) {
                continue;
            }

            $resultat[$cle] = $valeur;
        }

        return $resultat;
    }
}



function errorExeption(Exception $e)
{
    return response()->json(
        [
            'success' => false,
            'message' => config('app.debug') ? $e->getMessage() . ' - ' . $e->getLine() : 'Une Erreur est survenue vueillez contacter l\'administrateur : ' . $e->getMessage(),
        ],
        500
    );
}


function errorMessageValidation($validator)
{
    return response()->json(['success' => false, 'message' => $validator->errors()->all()], 422);
}

function errorNotFound($element)
{
    return response()->json(['success' => false, 'message' => $element . ' introuvable'], 422);
}

function errorMessage($message, $code = 400)
{
    return response()->json(['success' => false, 'message' => $message], $code);
}

function simpleSuccessMessage($message, $data = null, $code = 200)
{
    return response()->json(['success' => true, 'message' => $message, 'data' => $data], $code);
}

function successMessage($message, array $data = [])
{
    $response = array_merge(['success' => true, 'message' => $message], $data);
    return response()->json($response, 200);
}

function dispatchRoute(array $config, string $name)
{
    if (count($config) > 0 && isset($config[$name])) {
        return collect($config[$name]);
    }

    // Retourner une collection vide si la route n'est pas configurée
    return collect([
        'action' => null,
        'params' => [],
        'name' => $name,
    ]);
}

function buildRoute(string $action, array $params, string $name)
{
    return [
        'action' => $action,
        'params' => $params,
        'name' => $name
    ];
}

function makeRoutesfx($prefix, $controller, $name = null, $config = [])
{
    $name = $name ?: $prefix;
    $except = $config['except'] ?? [];

    Route::prefix($prefix)->group(function () use ($controller, $name, $except, $config) {
        $routes = [
            'index' => ['method' => 'get', 'path' => '/', 'default_action' => 'index'],
            'data' => ['method' => 'post', 'path' => '/data', 'default_action' => 'getData'],
            'store' => ['method' => 'post', 'path' => '/store', 'default_action' => 'store'],
            'update' => ['method' => 'post', 'path' => '/update/{id}', 'default_action' => 'update'],
            'toggle-active' => ['method' => 'post', 'path' => '/toggle-active/{id}', 'default_action' => 'toggleActive'],
            'search' => ['method' => 'get', 'path' => '/search', 'default_action' => 'search'],
            'delete' => ['method' => 'delete', 'path' => '/delete/{id}', 'default_action' => 'delete'],
        ];

        foreach ($routes as $routeName => $routeConfig) {
            if (!in_array($routeName, $except)) {
                $route = dispatchRoute($config, $routeName);
                Route::{$routeConfig['method']}(
                    $route->get('path', $routeConfig['path']),
                    [$controller, $route->get('action', null) ?? $routeConfig['default_action']]
                )->name($name . '.' . $route->get('name', $routeName) ?? $routeName);
            }
        }

        // Routes supplémentaires
        foreach ($config['additional'] ?? [] as $routeName => $additionalConfig) {
            if (!in_array($routeName, $except)) {
                Route::{$additionalConfig['method']}(
                    $additionalConfig['path'],
                    [$controller, $additionalConfig['action']]
                )->name($name . '.' . $routeName);
            }
        }
    });
}


function makeMatricule($prefix, $counter, $length = 6)
{
    $prefix = strtoupper($prefix);
    $year = date('y');
    return $prefix . $year . str_pad($counter, $length, '0', STR_PAD_LEFT);
}

function getNameFile($file)
{
    return pathinfo($file, PATHINFO_FILENAME) . '.' . pathinfo($file, PATHINFO_EXTENSION);
}

function makeFileName($name, UploadedFile $file, $prefix = null)
{
    $extension = $file->getClientOriginalExtension();
    $filename = str($prefix . '_' . $name . '.' . $extension);
    return $filename;
}

function transactional($callback)
{
    DB::beginTransaction();
    try {
        $callback();
        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Transaction failed: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine() . ' - ' . $e->getTraceAsString());
        return errorExeption($e);
    }
}


function _constantes()
{
    return new Constantes();
}

function default_photo()
{
    return asset('assets/images/avatar/user' . rand(1, 4) . '.png');
}

function calculeTaxeVariable(Contribuable $contribuable, Taxe $taxe, int $contribuableActiviteId, array $vars = [])
{
    // paramètres du contribuable
    $parametres = $contribuable->parametresByContribuableActivite($contribuableActiviteId)->get() ?? [];
    foreach ($parametres as $parametre) {
        $vars[$parametre->nom] = castValue($parametre->valeur, $parametre->type);
    }

    // constantes de la taxe
    foreach ($taxe->constantes ?? [] as $constante) {
        $vars[$constante->nom] = castValue($constante->valeur, $constante->type);
    }


    // 3. Évaluer la formule
    $expr = new ExpressionLanguage();

    return $expr->evaluate($taxe->formule, $vars);
}

function castValue($value, $type)
{
    return match ($type) {
        'int' => (int) $value,
        'decimal' => (float) $value,
        'bool' => (bool) $value,
        default => $value,
    };
}
