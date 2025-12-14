<?php

namespace App\PhpFx\Form;

/**
 * XForm - Classe pour construire des formulaires HTML dynamiques
 * Supporte Bootstrap, gestion d'erreurs, CSRF, méthodes chainées, layouts personnalisés et hooks
 */
class FormFx
{
    private $fields = [];
    private $formAttributes = [];
    private $useBootstrap = false;
    private $errors = [];
    private $csrfToken = null;
    private $wrapperClass = '';
    private $labelClass = '';
    private $inputClass = '';
    private $errorClass = 'text-danger';
    private $formClass = '';
    private $html = '';
    private $layout = 'linear'; // linear, grid, custom
    private $layoutConfig = [];
    private $hooks = [
        'before_form' => [],
        'after_form' => [],
        'before_field' => [],
        'after_field' => [],
        'before_render' => [],
        'after_render' => []
    ];
    private $sections = [];
    private $currentSection = null;

    /**
     * Constructeur
     */
    public function __construct($useBootstrap = false)
    {
        $this->useBootstrap = $useBootstrap;
        $this->setDefaultClasses();
    }

    /**
     * Méthode statique pour initialiser
     */
    public static function create($useBootstrap = false)
    {
        return new self($useBootstrap);
    }

    /**
     * Définit les classes par défaut selon Bootstrap ou non
     */
    private function setDefaultClasses()
    {
        if ($this->useBootstrap) {
            $this->wrapperClass = 'mb-3';
            $this->labelClass = 'form-label';
            $this->inputClass = 'form-control';
            $this->errorClass = 'text-danger small';
            $this->formClass = '';
        } else {
            $this->wrapperClass = 'field-wrapper';
            $this->labelClass = 'field-label';
            $this->inputClass = 'field-input';
            $this->errorClass = 'field-error';
            $this->formClass = 'form';
        }
    }

    /**
     * Active/désactive Bootstrap
     */
    public function bootstrap($enable = true)
    {
        $this->useBootstrap = $enable;
        $this->setDefaultClasses();
        return $this;
    }

    /**
     * Définit le layout du formulaire
     */
    public function layout($type, $config = [])
    {
        $this->layout = $type;
        $this->layoutConfig = $config;
        
        // Configuration automatique pour Bootstrap Grid
        if ($type === 'grid' && $this->useBootstrap && empty($config)) {
            $this->layoutConfig = [
                'container' => 'row',
                'field_wrapper' => 'col-md-6'
            ];
        }
        
        return $this;
    }

    /**
     * Définit les attributs du formulaire
     */
    public function form($attributes = [])
    {
        $this->formAttributes = array_merge($this->formAttributes, $attributes);
        return $this;
    }

    /**
     * Définit l'action du formulaire
     */
    public function action($action)
    {
        $this->formAttributes['action'] = $action;
        return $this;
    }

    /**
     * Définit la méthode du formulaire
     */
    public function method($method)
    {
        $this->formAttributes['method'] = $method;
        return $this;
    }

    /**
     * Active l'enctype pour les fichiers
     */
    public function multipart($enable = true)
    {
        if ($enable) {
            $this->formAttributes['enctype'] = 'multipart/form-data';
        } else {
            unset($this->formAttributes['enctype']);
        }
        return $this;
    }

    /**
     * Définit le token CSRF
     */
    public function csrf($token = true)
    {
        if ($token === true) {
            // Génération automatique du token CSRF
            if (function_exists('csrf_token')) {
                // Laravel
                $this->csrfToken = csrf_token();
            } elseif (isset($_SESSION)) {
                // PHP natif
                if (!isset($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
                $this->csrfToken = $_SESSION['csrf_token'];
            } else {
                // Fallback
                $this->csrfToken = bin2hex(random_bytes(32));
            }
        } elseif ($token === false) {
            $this->csrfToken = null;
        } else {
            $this->csrfToken = $token;
        }
        return $this;
    }

    /**
     * Définit les erreurs (pour Laravel Blade ou autres)
     */
    public function errors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Personnalise les classes CSS
     */
    public function classes($wrapper = null, $label = null, $input = null, $error = null, $form = null)
    {
        if ($wrapper !== null) $this->wrapperClass = $wrapper;
        if ($label !== null) $this->labelClass = $label;
        if ($input !== null) $this->inputClass = $input;
        if ($error !== null) $this->errorClass = $error;
        if ($form !== null) $this->formClass = $form;
        return $this;
    }

    /**
     * Ajoute un hook
     */
    public function addHook($event, $callback)
    {
        if (isset($this->hooks[$event])) {
            $this->hooks[$event][] = $callback;
        }
        return $this;
    }

    /**
     * Exécute les hooks
     */
    private function executeHooks($event, &$data = null)
    {
        if (isset($this->hooks[$event])) {
            foreach ($this->hooks[$event] as $callback) {
                if (is_callable($callback)) {
                    $result = call_user_func($callback, $data, $this);
                    if ($result !== null) {
                        $data = $result;
                    }
                }
            }
        }
    }

    /**
     * Démarre une section
     */
    public function startSection($name, $attributes = [])
    {
        $this->currentSection = $name;
        $this->sections[$name] = [
            'attributes' => $attributes,
            'fields' => [],
            'html' => ''
        ];
        return $this;
    }

    /**
     * Termine une section
     */
    public function endSection()
    {
        $this->currentSection = null;
        return $this;
    }

    /**
     * Ajoute du HTML personnalisé dans une section ou le formulaire principal
     */
    public function html($html)
    {
        if ($this->currentSection) {
            $this->sections[$this->currentSection]['html'] .= $html;
        } else {
            $this->html .= $html;
        }
        return $this;
    }

    /**
     * Ajoute des champs depuis un tableau
     */
    public function fields($fields)
    {
        foreach ($fields as $name => $config) {
            $this->addField($name, $config);
        }
        return $this;
    }

    /**
     * Ajoute un champ individuel
     */
    public function field($name, $config)
    {
        $this->addField($name, $config);
        return $this;
    }

    /**
     * Méthodes pour ajouter des champs spécifiques
     */
    public function text($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'text',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function hidden($name, $value = null, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'hidden',
            'value' => $value,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function email($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'email',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function password($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'password',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function number($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'number',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function select($name, $label, $options, $attributes = [], $class = '', $valueSelected = null)
    {
        return $this->field($name, [
            'type' => 'select',
            'label' => $label,
            'options' => $options,
            'attribute' => $attributes,
            'class' => $class,
            'valueSelected' => $valueSelected
        ]);
    }

    public function textarea($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'textarea',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function checkbox($name, $label, $value = 1, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'checkbox',
            'label' => $label,
            'value' => $value,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function radio($name, $label, $options, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'radio',
            'label' => $label,
            'options' => $options,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function file($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'file',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function range($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'range',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function tel($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'tel',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function url($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'url',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function search($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'search',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function time($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'time',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function week($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'week',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function month($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'month',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function date($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'date',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function datetime($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'datetime-local',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function color($name, $label, $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => 'color',
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function input($name, $type, $label = '', $attributes = [], $class = '')
    {
        return $this->field($name, [
            'type' => $type,
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function submit($value = 'Envoyer', $attributes = [], $class = '')
    {
        if ($this->useBootstrap && empty($class)) {
            $class = 'btn btn-primary';
        }
        return $this->field('submit', [
            'type' => 'submit',
            'value' => $value,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    public function button($value = 'Bouton', $attributes = [], $class = '')
    {
        if ($this->useBootstrap && empty($class)) {
            $class = 'btn btn-secondary';
        }
        return $this->field('btn-' . uniqid(), [
            'type' => 'button',
            'value' => $value,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    /**
     * NOUVELLES MÉTHODES POUR GÉNÉRER DES COMPOSANTS INDIVIDUELS
     */

    /**
     * Génère uniquement un input (sans wrapper, label, etc.)
     */
    public function renderInput($name, $type = 'text', $attributes = [], $value = null)
    {
        $id = $this->resolveId($name, $attributes);
        $class = $this->resolveClass($attributes, $this->inputClass);
        $value = $value ?? $this->getOldValue($name);
        
        $attrs = $this->buildAttributes(array_merge($attributes, ['id' => $id, 'class' => $class]));
        $valueAttr = !empty($value) ? ' value="' . htmlspecialchars($value) . '"' : '';
        
        return '<input type="' . $type . '" name="' . $name . '"' . $valueAttr . $attrs . '>';
    }

    /**
     * Génère uniquement un select (sans wrapper, label, etc.)
     */
    public function renderSelect($name, $options, $attributes = [], $valueSelected = null)
    {
        $id = $this->resolveId($name, $attributes);
        $class = $this->resolveClass($attributes, $this->inputClass);
        
        $attrs = $this->buildAttributes(array_merge($attributes, ['id' => $id, 'class' => $class]));
        $html = '<select name="' . $name . '"' . $attrs . '>';
        
        foreach ($options as $value => $text) {
            $selected = $this->isSelected($name, $value, $valueSelected) ? ' selected' : '';
            $html .= '<option value="' . htmlspecialchars($value) . '"' . $selected . '>' . htmlspecialchars($text) . '</option>';
        }
        
        $html .= '</select>';
        return $html;
    }

    /**
     * Génère uniquement une textarea (sans wrapper, label, etc.)
     */
    public function renderTextarea($name, $attributes = [], $value = null)
    {
        $id = $this->resolveId($name, $attributes);
        $class = $this->resolveClass($attributes, $this->inputClass);
        $value = $value ?? $this->getOldValue($name);
        
        $attrs = $this->buildAttributes(array_merge($attributes, ['id' => $id, 'class' => $class]));
        
        return '<textarea name="' . $name . '"' . $attrs . '>' . htmlspecialchars($value) . '</textarea>';
    }

    /**
     * Génère uniquement un label
     */
    public function renderLabel($name, $text, $attributes = [])
    {
        $for = $this->resolveId($name, $attributes);
        $class = $this->resolveClass($attributes, $this->labelClass);
        
        $attrs = $this->buildAttributes(array_merge($attributes, ['class' => $class]));
        
        return '<label for="' . $for . '"' . $attrs . '>' . htmlspecialchars($text) . '</label>';
    }

    /**
     * Résout l'ID en priorisant celui des attributes
     */
    private function resolveId($name, $attributes)
    {
        return isset($attributes['id']) ? $attributes['id'] : $name;
    }

    /**
     * Résout la classe en priorisant celle des attributes
     */
    private function resolveClass($attributes, $defaultClass)
    {
        return isset($attributes['class']) ? $attributes['class'] : $defaultClass;
    }

    /**
     * Ajoute un champ au formulaire
     */
    private function addField($name, $config)
    {
        if ($this->currentSection) {
            $this->sections[$this->currentSection]['fields'][$name] = $config;
        } else {
            $this->fields[$name] = $config;
        }
    }

    private function textRequired($attributes)
    {
        if (isset($attributes['required']) && $attributes['required']) {
            return '<span class="text-danger">*</span>';
        }
        return null;
    }

    /**
     * Génère le HTML d'un champ avec hooks
     */
    private function generateField($name, $config)
    {
        // Hook avant génération du champ
        $fieldData = ['name' => $name, 'config' => $config];
        $this->executeHooks('before_field', $fieldData);
        $name = $fieldData['name'];
        $config = $fieldData['config'];

        $type = $config['type'] ?? 'text';
        $label = $config['label'] ?? '';
        $attributes = $config['attribute'] ?? [];
        $class = $config['class'] ?? '';
        $options = $config['options'] ?? [];
        $value = $config['value'] ?? '';
        $valueSelected = $config['valueSelected'] ?? '';

        // Résolution de l'ID et de la classe
        $fieldId = $this->resolveId($name, $attributes);
        $fieldClass = $this->resolveClass($attributes, $this->inputClass);
        if (!empty($class)) {
            $fieldClass = $class;
        }

        // Gestion des erreurs
        $hasError = $this->hasError($name);
        $errorClass = $hasError ? ($this->useBootstrap ? 'is-invalid' : 'error') : '';
        if ($hasError && !empty($errorClass)) {
            $fieldClass .= ' ' . $errorClass;
        }

        $html = '';

        // Wrapper de début (sauf pour hidden et submit)
        if (!in_array($type, ['hidden', 'submit', 'button'])) {
            $wrapperClass = $this->getFieldWrapperClass();
            $html .= '<div class="' . $wrapperClass . '">';
        }

        // Label
        if (!empty($label) && !in_array($type, ['hidden', 'submit', 'button'])) {
            $html .= '<label for="' . $fieldId . '" class="' . $this->labelClass . '">' . $label . $this->textRequired($attributes) . '</label>';
        }

        // Champ selon le type
        switch ($type) {
            case 'select':
                $html .= $this->generateSelect($name, $options, $attributes, $fieldClass, $valueSelected);
                break;
            case 'textarea':
                $html .= $this->generateTextarea($name, $attributes, $fieldClass);
                break;
            case 'checkbox':
                $html .= $this->generateCheckbox($name, $config, $fieldClass);
                break;
            case 'radio':
                $html .= $this->generateRadio($name, $options, $attributes, $fieldClass);
                break;
            case 'submit':
                $html .= $this->generateSubmit($value ?: $label, $attributes, $fieldClass);
                break;
            case 'button':
                $html .= $this->generateButton($value ?: $label, $attributes, $fieldClass);
                break;
            case 'hidden':
                $html .= $this->generateInput($name, $type, $value, $attributes, '');
                break;
            default:
                $html .= $this->generateInput($name, $type, $value, $attributes, $fieldClass);
        }

        // Affichage des erreurs
        if ($hasError && !in_array($type, ['hidden', 'submit', 'button'])) {
            $html .= '<div class="' . $this->errorClass . '">' . $this->getError($name) . '</div>';
        }

        // Wrapper de fin
        if (!in_array($type, ['hidden', 'submit', 'button'])) {
            $html .= '</div>';
        }

        // Hook après génération du champ
        $this->executeHooks('after_field', $html);

        return $html;
    }

    /**
     * Obtient la classe wrapper selon le layout
     */
    private function getFieldWrapperClass()
    {
        switch ($this->layout) {
            case 'grid':
                return $this->layoutConfig['field_wrapper'] ?? $this->wrapperClass;
            default:
                return $this->wrapperClass;
        }
    }

    /**
     * Génère un input basique
     */
    private function generateInput($name, $type, $value, $attributes, $class)
    {
        $id = $this->resolveId($name, $attributes);
        $attrs = $this->buildAttributes($attributes);
        $valueAttr = !empty($value) ? ' value="' . htmlspecialchars($value) . '"' : ' value="'.$this->getOldValue($name).'"';
        $classAttr = !empty($class) ? ' class="' . $class . '"' : '';
        
        return '<input type="' . $type . '" name="' . $name . '" id="' . $id . '"' . $classAttr . $valueAttr . $attrs . '>';
    }

    /**
     * Génère un select
     */
    private function generateSelect($name, $options, $attributes, $class, $valueSelected = null)
    {
        $id = $this->resolveId($name, $attributes);
        $attrs = $this->buildAttributes($attributes);
        $html = '<select name="' . $name . '" id="' . $id . '" class="' . $class . '"' . $attrs . '>';
        
        foreach ($options as $value => $text) {
            $selected = $this->isSelected($name, $value, $valueSelected) ? ' selected' : '';
            $html .= '<option value="' . htmlspecialchars($value) . '"' . $selected . '>' . htmlspecialchars($text) . '</option>';
        }
        
        $html .= '</select>';
        return $html;
    }

    /**
     * Génère une textarea
     */
    private function generateTextarea($name, $attributes, $class)
    {
        $id = $this->resolveId($name, $attributes);
        $attrs = $this->buildAttributes($attributes);
        $value = $this->getOldValue($name);
        
        return '<textarea name="' . $name . '" id="' . $id . '" class="' . $class . '"' . $attrs . '>' . htmlspecialchars($value) . '</textarea>';
    }

    /**
     * Génère une checkbox
     */
    private function generateCheckbox($name, $config, $class)
    {
        $value = $config['value'] ?? 1;
        $attributes = $config['attribute'] ?? [];
        $id = $this->resolveId($name, $attributes);
        $attrs = $this->buildAttributes($attributes);
        $checked = $this->isChecked($name, $value) ? ' checked' : '';
        
        if ($this->useBootstrap) {
            return '<div class="form-check">
                        <input type="checkbox" name="' . $name . '" id="' . $id . '" value="' . $value . '" class="form-check-input"' . $checked . $attrs . '>
                        <label class="form-check-label" for="' . $id . '">' . $config['label'] . '</label>
                    </div>';
        } else {
            return '<input type="checkbox" name="' . $name . '" id="' . $id . '" value="' . $value . '" class="' . $class . '"' . $checked . $attrs . '>';
        }
    }

    /**
     * Génère des boutons radio
     */
    private function generateRadio($name, $options, $attributes, $class)
    {
        $html = '';
        $attrs = $this->buildAttributes($attributes);
        
        foreach ($options as $value => $text) {
            $checked = $this->isSelected($name, $value) ? ' checked' : '';
            $id = $name . '_' . $value;
            
            if ($this->useBootstrap) {
                $html .= '<div class="form-check">
                            <input type="radio" name="' . $name . '" id="' . $id . '" value="' . $value . '" class="form-check-input"' . $checked . $attrs . '>
                            <label class="form-check-label" for="' . $id . '">' . htmlspecialchars($text) . '</label>
                          </div>';
            } else {
                $html .= '<label><input type="radio" name="' . $name . '" id="' . $id . '" value="' . $value . '" class="' . $class . '"' . $checked . $attrs . '> ' . htmlspecialchars($text) . '</label>';
            }
        }
        
        return $html;
    }

    /**
     * Génère un bouton submit
     */
    private function generateSubmit($value, $attributes, $class)
    {
        $attrs = $this->buildAttributes($attributes);
        $classAttr = !empty($class) ? ' class="' . $class . '"' : '';
        
        return '<button type="submit"' . $classAttr . $attrs . '>' . htmlspecialchars($value) . '</button>';
    }

    /**
     * Génère un bouton
     */
    private function generateButton($value, $attributes, $class)
    {
        $attrs = $this->buildAttributes($attributes);
        $classAttr = !empty($class) ? ' class="' . $class . '"' : '';
        
        return '<button type="button"' . $classAttr . $attrs . '>' . htmlspecialchars($value) . '</button>';
    }

    /**
     * Construit les attributs HTML en excluant ceux déjà traités
     */
    private function buildAttributes($attributes)
    {
        $html = '';
        $excludedAttrs = ['id', 'class'];
        
        foreach ($attributes as $key => $value) {
            if (in_array($key, $excludedAttrs)) {
                continue;
            }
            
            if (is_bool($value)) {
                if ($value) {
                    $html .= ' ' . $key;
                }
            } else {
                $html .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
            }
        }
        return $html;
    }

    /**
     * Vérifie si un champ a une erreur
     */
    private function hasError($name)
    {
        return isset($this->errors[$name]) || (is_object($this->errors) && method_exists($this->errors, 'has') && $this->errors->has($name));
    }

    /**
     * Récupère l'erreur d'un champ
     */
    private function getError($name)
    {
        if (isset($this->errors[$name])) {
            return is_array($this->errors[$name]) ? $this->errors[$name][0] : $this->errors[$name];
        }
        
        if (is_object($this->errors) && method_exists($this->errors, 'first')) {
            return $this->errors->first($name);
        }
        
        return '';
    }

    /**
     * Récupère une ancienne valeur (pour Laravel old() ou session)
     */
    private function getOldValue($name)
    {
        // Pour Laravel
        if (function_exists('old')) {
            return old($name, '');
        }
        
        // Pour session PHP classique
        if (isset($_SESSION['old'][$name])) {
            return $_SESSION['old'][$name];
        }
        
        return '';
    }

    /**
     * Vérifie si une option est sélectionnée
     */
    private function isSelected($name, $value, $valueSelected = null)
    {
        $oldValue = $valueSelected ?? $this->getOldValue($name);
        return $oldValue == $value;
    }

    /**
     * Vérifie si une checkbox est cochée
     */
    private function isChecked($name, $value)
    {
        $oldValue = $this->getOldValue($name);
        return $oldValue == $value || (!empty($oldValue) && $value == 1);
    }

    public function mapOptions($data, $value, $text, $selectedText = null) {
        $options = $selectedText ? ['' => $selectedText] : [];
        foreach ($data as $item) {
            $options[$item[$value]] = $item[$text];
        }
        return $options;
    }

    /**
     * Génère le HTML des sections
     */
    private function renderSections()
    {
        $html = '';
        foreach ($this->sections as $name => $section) {
            $attrs = $this->buildAttributes($section['attributes']);
            $html .= '<div class="form-section" data-section="' . $name . '"' . $attrs . '>';
            
            // HTML personnalisé de la section
            $html .= $section['html'];
            
            // Champs de la section
            foreach ($section['fields'] as $fieldName => $fieldConfig) {
                $html .= $this->generateField($fieldName, $fieldConfig);
            }
            
            $html .= '</div>';
        }
        return $html;
    }

    /**
     * Génère le HTML selon le layout
     */
    private function renderWithLayout($content)
    {
        switch ($this->layout) {
            case 'grid':
                $containerClass = $this->layoutConfig['container'] ?? 'row';
                return '<div class="' . $containerClass . '">' . $content . '</div>';
            
            case 'custom':
                if (isset($this->layoutConfig['template']) && is_callable($this->layoutConfig['template'])) {
                    return call_user_func($this->layoutConfig['template'], $content, $this);
                }
                return $content;
                
            default: // linear
                return $content;
        }
    }

    /**
     * Génère le HTML complet du formulaire
     */
    public function render()
    {
        // Hook avant rendu
        $this->executeHooks('before_render');
        
        $html = '';
        
        // Hook avant formulaire
        $this->executeHooks('before_form', $html);
        
        // Ouverture du formulaire
        $formAttrs = $this->buildAttributes($this->formAttributes);
        $formClassAttr = !empty($this->formClass) ? ' class="' . $this->formClass . '"' : '';
        $html .= '<form' . $formClassAttr . $formAttrs . '>';
        
        // Token CSRF
        if ($this->csrfToken) {
            $html .= '<input type="hidden" name="_token" value="' . $this->csrfToken . '">';
        }

        // HTML personnalisé au début
        $html .= $this->html;
        
        // Génération du contenu des champs principaux
        $fieldsHtml = '';
        foreach ($this->fields as $name => $config) {
            $fieldsHtml .= $this->generateField($name, $config);
        }
        
        // Ajout des sections
        $fieldsHtml .= $this->renderSections();
        
        // Application du layout
        $html .= $this->renderWithLayout($fieldsHtml);
        
        // Hook après formulaire
        $this->executeHooks('after_form', $html);
        
        // Fermeture du formulaire
        $html .= '</form>';
        
        // Hook après rendu
        $this->executeHooks('after_render', $html);
        
        return $html;
    }

    /**
     * Alias pour render()
     */
    public function build()
    {
        return $this->render();
    }

    /**
     * Affiche directement le formulaire
     */
    public function show()
    {
        echo $this->render();
    }

    /**
     * Méthode magique pour affichage
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * NOUVELLES MÉTHODES UTILITAIRES
     */

    /**
     * Clone l'instance pour réutilisation
     */
    public function clone()
    {
        return clone $this;
    }

    /**
     * Réinitialise les champs tout en gardant la configuration
     */
    public function reset()
    {
        $this->fields = [];
        $this->sections = [];
        $this->currentSection = null;
        $this->html = '';
        return $this;
    }

    /**
     * Obtient un champ spécifique
     */
    public function getField($name)
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * Supprime un champ
     */
    public function removeField($name)
    {
        unset($this->fields[$name]);
        return $this;
    }

    /**
     * Modifie un champ existant
     */
    public function updateField($name, $config)
    {
        if (isset($this->fields[$name])) {
            $this->fields[$name] = array_merge($this->fields[$name], $config);
        }
        return $this;
    }

    /**
     * Ajoute une classe à un champ existant
     */
    public function addFieldClass($name, $class)
    {
        if (isset($this->fields[$name])) {
            $currentClass = $this->fields[$name]['class'] ?? '';
            $this->fields[$name]['class'] = trim($currentClass . ' ' . $class);
        }
        return $this;
    }

    /**
     * Active/désactive un champ
     */
    public function disableField($name, $disable = true)
    {
        if (isset($this->fields[$name])) {
            $attributes = $this->fields[$name]['attribute'] ?? [];
            if ($disable) {
                $attributes['disabled'] = true;
            } else {
                unset($attributes['disabled']);
            }
            $this->fields[$name]['attribute'] = $attributes;
        }
        return $this;
    }

    /**
     * Rend un champ requis
     */
    public function requireField($name, $required = true)
    {
        if (isset($this->fields[$name])) {
            $attributes = $this->fields[$name]['attribute'] ?? [];
            if ($required) {
                $attributes['required'] = true;
            } else {
                unset($attributes['required']);
            }
            $this->fields[$name]['attribute'] = $attributes;
        }
        return $this;
    }

    /**
     * Ajoute un placeholder à un champ
     */
    public function placeholder($name, $placeholder)
    {
        if (isset($this->fields[$name])) {
            $attributes = $this->fields[$name]['attribute'] ?? [];
            $attributes['placeholder'] = $placeholder;
            $this->fields[$name]['attribute'] = $attributes;
        }
        return $this;
    }

    /**
     * Définit une valeur par défaut pour un champ
     */
    public function defaultValue($name, $value)
    {
        if (isset($this->fields[$name])) {
            $this->fields[$name]['value'] = $value;
        }
        return $this;
    }

    /**
     * Groupe plusieurs champs dans une div
     */
    public function group($fields, $class = 'form-group', $attributes = [])
    {
        $groupHtml = '<div class="' . $class . '"' . $this->buildAttributes($attributes) . '>';
        foreach ($fields as $fieldName) {
            if (isset($this->fields[$fieldName])) {
                $groupHtml .= $this->generateField($fieldName, $this->fields[$fieldName]);
                unset($this->fields[$fieldName]); // Remove from main fields to avoid duplication
            }
        }
        $groupHtml .= '</div>';
        
        $this->html($groupHtml);
        return $this;
    }

    /**
     * Ajoute un fieldset
     */
    public function fieldset($legend, $fields, $attributes = [])
    {
        $fieldsetHtml = '<fieldset' . $this->buildAttributes($attributes) . '>';
        if ($legend) {
            $fieldsetHtml .= '<legend>' . htmlspecialchars($legend) . '</legend>';
        }
        
        foreach ($fields as $fieldName) {
            if (isset($this->fields[$fieldName])) {
                $fieldsetHtml .= $this->generateField($fieldName, $this->fields[$fieldName]);
                unset($this->fields[$fieldName]);
            }
        }
        
        $fieldsetHtml .= '</fieldset>';
        $this->html($fieldsetHtml);
        return $this;
    }

    /**
     * Ajoute des colonnes Bootstrap
     */
    public function columns($fieldsConfig)
    {
        if (!$this->useBootstrap) {
            return $this;
        }

        $html = '<div class="row">';
        foreach ($fieldsConfig as $config) {
            $colClass = $config['class'] ?? 'col-md-6';
            $fields = $config['fields'] ?? [];
            
            $html .= '<div class="' . $colClass . '">';
            foreach ($fields as $fieldName) {
                if (isset($this->fields[$fieldName])) {
                    $html .= $this->generateField($fieldName, $this->fields[$fieldName]);
                    unset($this->fields[$fieldName]);
                }
            }
            $html .= '</div>';
        }
        $html .= '</div>';
        
        $this->html($html);
        return $this;
    }

    /**
     * Ajoute du contenu conditionnel
     */
    public function conditional($condition, $callback)
    {
        if ($condition) {
            if (is_callable($callback)) {
                call_user_func($callback, $this);
            }
        }
        return $this;
    }

    /**
     * Applique une configuration de thème
     */
    public function theme($theme)
    {
        $themes = [
            'material' => [
                'form' => 'material-form',
                'wrapper' => 'material-field',
                'input' => 'material-input',
                'label' => 'material-label'
            ],
            'bulma' => [
                'form' => '',
                'wrapper' => 'field',
                'input' => 'input',
                'label' => 'label'
            ]
        ];

        if (isset($themes[$theme])) {
            $config = $themes[$theme];
            $this->classes(
                $config['wrapper'] ?? null,
                $config['label'] ?? null,
                $config['input'] ?? null,
                null,
                $config['form'] ?? null
            );
        }
        
        return $this;
    }

    /**
     * Support pour les validation rules HTML5
     */
    public function rules($name, $rules)
    {
        if (!isset($this->fields[$name])) {
            return $this;
        }

        $attributes = $this->fields[$name]['attribute'] ?? [];
        
        if (is_array($rules)) {
            foreach ($rules as $rule => $value) {
                switch ($rule) {
                    case 'required':
                        if ($value) $attributes['required'] = true;
                        break;
                    case 'min':
                        $attributes['min'] = $value;
                        break;
                    case 'max':
                        $attributes['max'] = $value;
                        break;
                    case 'minlength':
                        $attributes['minlength'] = $value;
                        break;
                    case 'maxlength':
                        $attributes['maxlength'] = $value;
                        break;
                    case 'pattern':
                        $attributes['pattern'] = $value;
                        break;
                }
            }
        }
        
        $this->fields[$name]['attribute'] = $attributes;
        return $this;
    }
}