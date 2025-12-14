<?php

namespace App\PhpFx\Form;

/**
 * XForm - Classe pour construire des formulaires HTML dynamiques
 * Supporte Bootstrap, gestion d'erreurs, CSRF, et méthodes chainées
 */
class XForm
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
    private $fieldTemplates = [];

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
            $this->csrfToken = $this->generateCsrfToken();
        } elseif ($token === false) {
            $this->csrfToken = null;
        } else {
            $this->csrfToken = $token;
        }
        return $this;
    }

    /**
     * Génère un token CSRF
     */
    private function generateCsrfToken()
    {
        if (function_exists('csrf_token')) {
            return csrf_token();
        } elseif (isset($_SESSION)) {
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            return $_SESSION['csrf_token'];
        }
        return bin2hex(random_bytes(32));
    }

    /**
     * Récupère le token CSRF
     */
    private function getCsrfToken()
    {
        if ($this->csrfToken === null) {
            $this->csrf();
        }
        return $this->csrfToken;
    }

    /**
     * Définit les erreurs
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

    public function html($html)
    {
        $this->html = $html;
        return $this;
    }

    /**
     * Méthodes pour ajouter des champs spécifiques
     */
    public function text($name, $label, $attributes = [], $value = null, $class = '')
    {
        return $this->field($name, [
            'type' => 'text',
            'label' => $label,
            'attribute' => $attributes,
            'value' => $value ?? $attributes['value'] ?? '',
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

    public function checkbox($name, $label, $value = 1, $attributes = [], $class = '', $textSelected = '')
    {
        return $this->field($name, [
            'type' => 'checkbox',
            'label' => $label,
            'value' => $value,
            'attribute' => $attributes,
            'class' => $class,
            'textSelected' => $textSelected
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

    public function button($value = 'Envoyer', $attributes = [], $class = '')
    {
        if ($this->useBootstrap && empty($class)) {
            $class = 'btn btn-primary';
        }
        return $this->field('btn-color', [
            'type' => 'button',
            'value' => $value,
            'attribute' => $attributes,
            'class' => $class
        ]);
    }

    /**
     * Ajoute un champ au formulaire
     */
    private function addField($name, $config)
    {
        $this->fields[$name] = $config;
    }

    private function textRequired($name)
    {
        if (isset($this->fields[$name]['attribute']['required']) && $this->fields[$name]['attribute']['required']) {
            return '<span class="text-danger">*</span>';
        }
        return null;
    }

    /**
     * Génère le HTML d'un champ
     */
    private function generateField($name, $config)
    {
        $type = $config['type'] ?? 'text';
        $label = $config['label'] ?? '';
        $attributes = $config['attribute'] ?? [];
        $class = $config['class'] ?? '';
        $options = $config['options'] ?? [];
        $value = $config['value'] ?? '';
        $valueSelected = $config['valueSelected'] ?? '';
        $prefix = $config['prefix'] ?? '';
        $suffix = $config['suffix'] ?? '';
        $textSelected = $config['textSelected'] ?? '';
        $wrapperClass = $config['wrapperClass'] ?? $this->wrapperClass;

        // Gestion de l'ID - priorité à l'attribut spécifié
        $id = $attributes['id'] ?? $name;

        // Classe CSS pour le champ
        $fieldClass = $this->inputClass;
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
            $html .= '<div class="' . $wrapperClass . '">';
        }

        // Préfixe
        if (!empty($prefix)) {
            $html .= $prefix;
        }

        // Label
        if (!empty($label) && !in_array($type, ['hidden', 'submit', 'button'])) {
            $html .= '<label for="' . $id . '" class="' . $this->labelClass . '">' . $label . $this->textRequired($name) . '</label>';
        }

        // Champ selon le type
        switch ($type) {
            case 'select':
                $html .= $this->generateSelect($name, $options, $attributes, $fieldClass, $valueSelected, $id);
                break;
            case 'textarea':
                $html .= $this->generateTextarea($name, $attributes, $fieldClass, $id, $value);
                break;
            case 'checkbox':
                $html .= $this->generateCheckbox($name, $config, $fieldClass, $id, $textSelected);
                break;
            case 'radio':
                $html .= $this->generateRadio($name, $options, $attributes, $fieldClass, $id);
                break;
            case 'submit':
                $html .= $this->generateSubmit($value ?: $label, $attributes, $fieldClass, $id);
                break;
            case 'button':
                $html .= $this->generateButton($value ?: $label, $attributes, $fieldClass, $id);
                break;
            case 'hidden':
                $html .= $this->generateInput($name, $type, $value, $attributes, '', $id);
                break;
            default:
                $html .= $this->generateInput($name, $type, $value, $attributes, $fieldClass, $id);
        }

        // Suffixe
        if (!empty($suffix)) {
            $html .= $suffix;
        }

        // Affichage des erreurs
        if ($hasError && !in_array($type, ['hidden', 'submit', 'button'])) {
            $html .= '<div class="' . $this->errorClass . '">' . $this->getError($name) . '</div>';
        }

        // Wrapper de fin
        if (!in_array($type, ['hidden', 'submit', 'button'])) {
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Génère un input basique
     */
    private function generateInput($name, $type, $value, $attributes, $class, $id)
    {
        $attrs = $this->buildAttributes($attributes);
        $valueAttr = ' value="' . htmlspecialchars($this->getFieldValue($name, $value)) . '"';
        $classAttr = !empty($class) ? ' class="' . $class . '"' : '';

        return '<input type="' . $type . '" name="' . $name . '" id="' . $id . '"' . $classAttr . $valueAttr . $attrs . '>';
    }

    /**
     * Génère un select
     */
    private function generateSelect($name, $options, $attributes, $class, $valueSelected, $id)
    {
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
    private function generateTextarea($name, $attributes, $class, $id, $value)
    {
        $attrs = $this->buildAttributes($attributes);
        $value = $value ??  $this->getOldValue($name) ?? '';

        return '<textarea name="' . $name . '" id="' . $id . '" class="' . $class . '"' . $attrs . '>' . htmlspecialchars($value) . '</textarea>';
    }

    /**
     * Génère une checkbox
     */
    private function generateCheckbox($name, $config, $class, $id, $textSelected = null)
    {
        $value = $config['value'] ?? 1;
        $attributes = $config['attribute'] ?? [];
        $attrs = $this->buildAttributes($attributes);
        $checked = $this->isChecked($name, $value) ? ' checked' : '';

        if ($this->useBootstrap) {
            return '<div class="form-check">
                        <input type="checkbox" name="' . $name . '" id="' . $id . '" value="' . $value . '" class="form-check-input"' . $checked . $attrs . '>
                        <label class="form-check-label" for="' . $id . '">' . $textSelected . '</label>
                    </div>';
        } else {
            return '<input type="checkbox" name="' . $name . '" id="' . $id . '" value="' . $value . '" class="' . $class . '"' . $checked . $attrs . '>';
        }
    }

    /**
     * Génère des boutons radio
     */
    private function generateRadio($name, $options, $attributes, $class, $id)
    {
        $html = '';
        $attrs = $this->buildAttributes($attributes);

        foreach ($options as $value => $text) {
            $radioId = $id . '_' . $value;
            $checked = $this->isSelected($name, $value) ? ' checked' : '';

            if ($this->useBootstrap) {
                $html .= '<div class="form-check">
                            <input type="radio" name="' . $name . '" id="' . $radioId . '" value="' . $value . '" class="form-check-input"' . $checked . $attrs . '>
                            <label class="form-check-label" for="' . $radioId . '">' . htmlspecialchars($text) . '</label>
                          </div>';
            } else {
                $html .= '<label><input type="radio" name="' . $name . '" id="' . $radioId . '" value="' . $value . '" class="' . $class . '"' . $checked . $attrs . '> ' . htmlspecialchars($text) . '</label>';
            }
        }

        return $html;
    }

    /**
     * Génère un bouton submit
     */
    private function generateSubmit($value, $attributes, $class, $id)
    {
        $attrs = $this->buildAttributes($attributes);
        $classAttr = !empty($class) ? ' class="' . $class . '"' : '';

        return '<button type="submit" id="' . $id . '"' . $classAttr . $attrs . '>' . htmlspecialchars($value) . '</button>';
    }

    /**
     * Génère un bouton
     */
    private function generateButton($value, $attributes, $class, $id)
    {
        $attrs = $this->buildAttributes($attributes);
        $classAttr = !empty($class) ? ' class="' . $class . '"' : '';

        return '<button type="button" id="' . $id . '"' . $classAttr . $attrs . '>' . htmlspecialchars($value) . '</button>';
    }

    /**
     * Construit les attributs HTML
     */
    private function buildAttributes($attributes, $isform = false)
    {
        $html = '';
        foreach ($attributes as $key => $value) {
            //if ($key === 'id' && !$isform) continue; // On ne veut pas dupliquer l'ID

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
     * Récupère la valeur d'un champ avec priorité
     */
    private function getFieldValue($name, $defaultValue = '')
    {
        $oldValue = $this->getOldValue($name);
        if (!empty($oldValue)) {
            return $oldValue;
        }
        return $defaultValue;
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
            $value = old($name);
            if (!is_null($value)) return $value;
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

    /**
     * Génère le HTML complet du formulaire
     */
    public function render()
    {
        $html = '';

        // Ouverture du formulaire
        $formAttrs = $this->buildAttributes($this->formAttributes);
        $formClassAttr = !empty($this->formClass) ? ' class="' . $this->formClass . '"' : '';
        $html .= '<form' . $formClassAttr . $formAttrs . '>';

        // Token CSRF
        if ($this->csrfToken) {
            $html .= '<input type="hidden" name="_token" value="' . $this->csrfToken . '">';
        }

        $html .= $this->html;

        // Génération des champs
        foreach ($this->fields as $name => $config) {
            $html .= $this->generateField($name, $config);
        }

        // Fermeture du formulaire
        $html .= '</form>';

        return $html;
    }

    /**
     * Rend un seul champ sans formulaire
     */
    public function renderField($name, $noBuild = false)
    {
        if (!isset($this->fields[$name])) {
            throw new \InvalidArgumentException("Field {$name} does not exist");
        }
        $field = $this->fields[$name];
        if ($noBuild) {
            unset($this->fields[$name]);
        }
        return $this->generateField($name, $field);
    }

    /**
     * Méthode statique pour générer un champ rapide
     */
    public static function quickField($type, $name, $label = '', $attributes = [], $class = '')
    {
        $form = new self(false);
        return $form->field($name, [
            'type' => $type,
            'label' => $label,
            'attribute' => $attributes,
            'class' => $class
        ])->renderField($name);
    }

    /**
     * Ouvre un formulaire
     */
    public function open($action = '', $method = 'POST', $attributes = [])
    {
        $attrs = array_merge([
            'action' => $action,
            'method' => $method
        ], $attributes);

        $formAttrs = $this->buildAttributes($attrs);
        $formClassAttr = !empty($this->formClass) ? ' class="' . $this->formClass . '"' : '';
        return '<form' . $formClassAttr . $formAttrs . '>';
    }

    /**
     * Ferme un formulaire
     */
    public static function close()
    {
        return '</form>';
    }

    /**
     * Génère un token CSRF seul
     */
    public function token()
    {
        return '<input type="hidden" name="_token" value="' . $this->getCsrfToken() . '">';
    }

    /**
     * Ajoute un préfixe à un champ
     */
    public function prefix($name, $content)
    {
        if (isset($this->fields[$name])) {
            $this->fields[$name]['prefix'] = $content;
        }
        return $this;
    }

    /**
     * Ajoute un suffixe à un champ
     */
    public function suffix($name, $content)
    {
        if (isset($this->fields[$name])) {
            $this->fields[$name]['suffix'] = $content;
        }
        return $this;
    }

    /**
     * Rend un groupe de champs
     */
    public function group($fields, $wrapperAttributes = [])
    {
        $html = '<div ' . $this->buildAttributes($wrapperAttributes) . '>';
        foreach ($fields as $field) {
            $html .= $this->renderField($field);
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * Rend des champs cachés groupés
     */
    public function hiddenFields(array $fields)
    {
        $html = '';
        foreach ($fields as $name => $value) {
            $html .= $this->hidden($name, $value);
        }
        return $html;
    }

    /**
     * Alias pour render()
     */
    public function build()
    {
        return $this->render();
    }

    public function mapOptions($data, $value, $text, $selectedText = null)
    {
        $options = $selectedText ? ['' => $selectedText] : [];
        if (strpos($text, '-') !== false) {
            $text = explode('-', $text);
            $text1 = $text[0];
            $text2 = $text[1];
            foreach ($data as $item) {
                $options[$item[$value]] = (isset($item[$text1]) ? $item[$text1] : '') . ' - ' . (isset($item[$text2]) ? $item[$text2] : ''); 
            }
            return $options;
        } else {
            foreach ($data as $item) {
                $options[$item[$value]] = $item[$text];
            }
        }
        return $options;
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
}
