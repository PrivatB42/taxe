<?php

namespace App\PhpFx\Form;

/**
 * FormMultiStep - Classe pour créer des formulaires multi-étapes personnalisables
 * 
 * @author YourName
 * @version 1.1
 */
class FormMultiStep
{
    private array $steps = [];
    private array $attributes = [];
    private array $config = [];
    private $callJs = null;
    private $formId = 'formId';
    private $rules = [];

    /**
     * Configuration par défaut
     */
    private array $defaultConfig = [
        'animation' => 'fadeIn',
        'progress' => [
            'selectcolor' => 'primary',
            'type' => 'bar', // bar, stepsTitle, stepsTitleRound, barRound, steps, stepsBar, stepsBarTitle, stepsTitleRound2
            'bgcolor' => 'secondary',
            'textcolor' => 'white',
        ],
        'submitButton' => ['text' => 'Soumettre', 'class' => 'btn btn-primary', 'type' => 'submit', 'icon' => 'fas fa-paper-plane'],
        'nextButton' => ['text' => 'Suivant', 'class' => 'btn btn-primary', 'icon' => 'fas fa-arrow-right'],
        'prevButton' => ['text' => 'Précedent', 'class' => 'btn btn-secondary', 'icon' => 'fas fa-arrow-left'],
        'show_progress' => true,
        'validate_step' => true,
        'ajax_submit' => true,
        'success_redirect' => null,
        'wrapper_class' => 'multi-step-form card p-5',
    ];

    /**
     * Constructeur
     */
    public function __construct(string $formId, array $config = [])
    {
        $this->config = array_merge($this->defaultConfig, $config);
        $this->formId = $formId;

        if (!empty($config['progress'])) {
            if (!$this->itemConfig('progress.type')) $this->config['progress']['type'] = 'bar';
            if (!$this->itemConfig('progress.bgcolor')) $this->config['progress']['bgcolor'] = 'secondary';
            if (!$this->itemConfig('progress.textcolor')) $this->config['progress']['textcolor'] = 'white';
            if (!$this->itemConfig('progress.selectcolor')) $this->config['progress']['selectcolor'] = 'primary';
        }
    }

    public function getFormId()
    {
        return $this->formId;
    }

    public static function initialCss()
    {
        return '
        /* Multi-step progress */
        <style>
        .step-progress .step-labels {
            display: flex;
            justify-content: space-between;
        }
        .step-label {
            font-size: 0.875rem;
            color: #6c757d;
            flex: 1;
            text-align: center;
        }
        .step-label.active {
            color: #fd0d5dff;
            font-weight: bold;
        }
        </style>
        ';
    }

    public function createForm($steps, $attributes = [], bool $submit = false)
    {
        $attributes = array_merge([
            'action' => '',
            'method' => 'POST'
        ], $attributes);

        $type = $submit ? 'submit' : 'button';

        $html = '<div class="' . $this->escape($this->itemConfig('wrapper_class')) . '" id="' . $this->escape($this->formId) . '">';

        // Indicateurs de steps Bootstrap avec navigation (stepsTitle ou stepsTitleRound)
        if ($this->itemConfig('progress.type') === 'stepsTitle' || $this->itemConfig('progress.type') === 'stepsTitleRound') {
            $html .= '<div class="step-indicator mb-4">';
            $html .= '<ul class="nav nav-pills nav-justified bg-' . $this->itemConfig('progress.bgcolor') . '">';
            
            foreach ($steps as $index => $step) {
                $active = $index === 0 ? 'active' : '';
                $completed = $index < 1 ? 'completed' : '';
                $rounded = $this->itemConfig('progress.type') === 'stepsTitleRound' ? 'rounded-pill' : '';
                $bgColor = $active == 'active' ? 'bg-' . $this->itemConfig('progress.selectcolor') : '';
                $stepClass = $active . ' ' . $completed . ' ' . $bgColor . ' ' . $rounded;
                
                $html .= '<li class="nav-item">';
                $html .= '<button type="button" class="nav-link step-indicator-item ' . $stepClass . ' text-' . $this->config['progress']['textcolor'] . ' ' . $stepClass . '" data-step="' . $index . '" onclick="goToStep(' . $index . ')" ' . ($index > 1 ? 'disabled' : '') . '>';
                $html .= '<span class="step-number">' . ($index + 1) . '</span>';
                $html .= '<span class="step-title">' . $this->escape($step['title']) . '</span>';
                $html .= '</button>';
                $html .= '</li>';
            }
            
            $html .= '</ul>';
            $html .= '</div>';
        }

        // Version compacte (bar ou barRound)
        if ($this->itemConfig('progress.type') === 'bar' || $this->itemConfig('progress.type') === 'barRound') {
            $html .= '<div class="d-flex align-items-center mb-4">';
            $html .= '<div class="progress flex-grow-1" style="height: 8px;">';
            $html .= '<div class="progress-bar bg-' . $this->itemConfig('progress.selectcolor') . '" id="step-progress-bar" style="width: ' . (100 / count($steps)) . '%"></div>';
            $html .= '</div>';
            
            $html .= '<div class="ms-3">';
            foreach ($steps as $index => $step) {
                $active = $index === 0 ? 'active' : '';
                $completed = $index < 1 ? 'completed' : '';
                $rounded = $this->itemConfig('progress.type') === 'barRound' ? 'rounded-pill' : '';
                $bgColor = $active == 'active' ? 'bg-' . $this->itemConfig('progress.selectcolor') . ' bg-secondary' : ' bg-secondary';
                $stepClass = $active . ' ' . $completed . ' ' . $bgColor . ' ' . $rounded;
                
                $html .= '<button type="button" class="badge ' . $stepClass . ' step-indicator-item me-1" data-step="' . $index . '" onclick="goToStep(' . $index . ')" ' . ($index > 0 ? 'disabled' : '') . '>';
                $html .= ($index + 1);
                $html .= '</button>';
            }
            $html .= '</div>';
            $html .= '</div>';
        }

        // Barre de progression avec indicateurs (stepsBar, stepsBarTitle, steps, stepsTitleRound2)
        if (in_array($this->itemConfig('progress.type'), ['stepsBar', 'stepsBarTitle', 'steps', 'stepsTitleRound2'])) {
            $html .= '<div class="mb-4 ms-4 me-4">';
            
            if ($this->itemConfig('progress.type') === 'stepsBarTitle' || $this->itemConfig('progress.type') === 'stepsBar') {
                $html .= '<div class="progress" style="height: 8px;">';
                $html .= '<div class="progress-bar bg-' . $this->itemConfig('progress.selectcolor') . '" id="step-progress-bar" style="width: ' . (100 / count($steps)) . '%"></div>';
                $html .= '</div>';
            }
            
            $html .= '<div class="d-flex justify-content-between position-relative mt-2">';
            foreach ($steps as $index => $step) {
                $active = $index === 0 ? 'active' : '';
                $completed = $index < 1 ? 'completed' : '';
                $bgColor = $active == 'active' ? 'bg-' . $this->itemConfig('progress.selectcolor') . ' bg-secondary' : ' bg-secondary';
                $stepClass = $active . ' ' . $completed . ' ' . $bgColor;
                $left = count($steps) === 1 ? '50' : ($index / (count($steps) - 1)) * 100;
                
                $html .= '<button type="button" class="position-absolute step-indicator-item translate-middle btn-sm p-0 rounded-circle ' . $stepClass . '" data-step="' . $index . '" onclick="goToStep(' . $index . ')" style="left: ' . $left . '%; width: 30px; height: 30px;">';
                $html .= '<span class="text-white fw-bold">' . ($index + 1) . '</span>';
                $html .= '</button>';
            }
            $html .= '</div>';
            
            if ($this->itemConfig('progress.type') === 'stepsTitleRound2' || $this->itemConfig('progress.type') === 'stepsBarTitle') {
                $html .= '<div class="d-flex justify-content-between mt-3">';
                foreach ($steps as $index => $step) {
                    $html .= '<button type="button" class="btn btn-sm btn-link p-0 text-decoration-none text-muted" data-step="' . $index . '" onclick="goToStep(' . $index . ')" ' . ($index > 0 ? 'disabled' : '') . '>';
                    $html .= $this->escape($step['title']);
                    $html .= '</button>';
                }
                $html .= '</div>';
            }
            
            $html .= '</div>';
        }

        // Début du formulaire
        $html .= '<form id="form_' . $this->escape($this->formId) . '" ' . $this->buildAttributes($attributes) . '>';
        $html .= csrf_field();
        
        // Génération des steps
        foreach ($steps as $index => $step) {
            $display = $index === 0 ? 'block' : 'none';
            $html .= '<div class="step" id="' . $this->formId . '_step_' . $index . '" style="display: ' . $display . ';">';
            
            if (in_array($this->itemConfig('progress.type'), ['stepsBar', 'bar', 'steps', 'barRound'])) {
                $html .= '<h4>' . $this->escape($step['title']) . '</h4>';
            }
            
            if (isset($step['description'])) {
                $html .= '<p class="text-muted">' . $this->escape($step['description']) . '</p>';
            }
            
            $html .= '<div class="text-danger mb-4" id="alert_' . $this->formId . '_step_' . $index . '"></div>';
            $html .= $step['content'];
            $html .= '</div>';
        }
        
        // Boutons de navigation
        $html .= '<div class="step-navigation d-flex justify-content-start gap-3 mt-4">';
        $html .= '<button type="button" class="' . ($this->config['prevButton']['class'] ?? '') . ' ' . $this->formId . 'prev-step" style="display:none;">';
        if (isset($this->config['prevButton']['icon'])) {
            $html .= '<i class="' . $this->config['prevButton']['icon'] . '"></i> ';
        }
        $html .= $this->config['prevButton']['text'] ?? '';
        $html .= '</button>';
        
        $html .= '<button type="button" class="' . ($this->config['nextButton']['class'] ?? '') . ' ' . $this->formId . 'next-step">';
        if (isset($this->config['nextButton']['icon'])) {
            $html .= '<i class="' . $this->config['nextButton']['icon'] . '"></i> ';
        }
        $html .= $this->config['nextButton']['text'] ?? '';
        $html .= '</button>';
        
        $html .= '<button type="'.$type.'" name="submit" id="save_' . $this->formId . '" class="' . ($this->config['submitButton']['class'] ?? '') . ' ' . $this->formId . 'submit-form" style="display:none;">';
        if (isset($this->config['submitButton']['icon'])) {
            $html .= '<i class="' . $this->config['submitButton']['icon'] . '"></i> ';
        }
        $html .= $this->config['submitButton']['text'] ?? '';
        $html .= '</button>';
        $html .= '</div>';
        
        $html .= '</form>';
        $html .= '</div>';
        
        // Ajouter le script JavaScript
        $html .= self::getMultiStepScript($this->formId, count($steps), $this->config, $this->callJs, $this->rules);
        
        return $html;
    }

    private static function getMultiStepScript($formId, $stepCount, $config, $callback = null, $rules = null)
    {
        $script = '<script>
            document.addEventListener(\'DOMContentLoaded\', function() {
                let currentStep = 0;
                const formId = \'' . $formId . '\';
                const stepColor = \'' . $config['progress']['selectcolor'] . '\';
                const totalSteps = ' . $stepCount . ';
                const form = document.getElementById(\'form_\' + formId);
                const callback = \'' . $callback . '\';
                const rules = JSON.parse(\'' . json_encode($rules) . '\');
                const config = JSON.parse(\'' . json_encode($config) . '\');

                // Désactiver la validation native du navigateur
                form.setAttribute(\'novalidate\', \'novalidate\');

                window.FormFx = window.FormFx || {};
                window.FormFx.multiStepForms = window.FormFx.multiStepForms || {};
                window.FormFx.multiStepForms[formId] = {
                    currentStep: currentStep,
                    totalSteps: totalSteps
                };

                // Fonction pour naviguer vers une étape spécifique
                window.goToStep = function(stepIndex) {
                    if (stepIndex >= 0 && stepIndex <= currentStep) {
                        currentStep = stepIndex;
                        showStep(currentStep);
                        updateProgress();
                        updateButtons();
                        updateStepIndicators();
                    }
                };

                // Gestionnaire pour le bouton Suivant
                document.querySelectorAll(`.${formId}next-step`).forEach(button => {
                    button.addEventListener(\'click\', function() {
                        if (validateCurrentStep()) {
                            if (currentStep < totalSteps - 1) {
                                currentStep++;
                                showStep(currentStep);
                                updateProgress();
                                updateButtons();
                            }
                        }
                    });
                });

                // Gestionnaire pour le bouton Précédent
                document.querySelectorAll(`.${formId}prev-step`).forEach(button => {
                    button.addEventListener(\'click\', function() {
                        if (currentStep > 0) {
                            currentStep--;
                            showStep(currentStep);
                            updateProgress();
                            updateButtons();
                        }
                    });
                });

                // Gestionnaire pour le bouton Envoyer
                document.querySelectorAll(`.${formId}submit-form`).forEach(button => {
                    button.addEventListener(\'click\', function(e) {
                        if (!validateAllSteps()) {
                            e.preventDefault();
                            // Aller à la première étape avec erreur
                            goToFirstInvalidStep();
                        }
                    });
                });

                function showStep(step) {
                    document.querySelectorAll(\'.step\').forEach(stepElement => {
                        stepElement.style.display = \'none\';
                    });

                    const currentStepElement = document.getElementById(formId + \'_step_\' + step);
                    if (currentStepElement) {
                        currentStepElement.style.display = \'block\';
                    }

                    document.querySelectorAll(\'.step-label\').forEach((label, index) => {
                        label.classList.remove(\'active\');
                        if (index === step) {
                            label.classList.add(\'active\');
                        }
                    });

                    document.querySelectorAll(\'.step-indicator-item\').forEach((item, index) => {
                        item.classList.remove(\'active\');
                        item.classList.remove(`bg-${stepColor}`);
                        if (index === step) {
                            item.classList.add(\'active\');
                            item.classList.add(`bg-${stepColor}`);
                        }
                    });

                    const alertElement = document.getElementById(`alert_${formId}_step_${step}`);
                    if (alertElement) {
                        alertElement.innerHTML = \'\';
                    }
                }

                function updateProgress() {
                    const progress = ((currentStep + 1) / totalSteps) * 100;
                    const progressBar = document.querySelector(\'.progress-bar\');
                    if (progressBar) {
                        progressBar.style.width = progress + \'%\';
                    }
                }

                function updateButtons() {
                    const prevButtons = document.querySelectorAll(`.${formId}prev-step`);
                    const nextButtons = document.querySelectorAll(`.${formId}next-step`);
                    const submitButtons = document.querySelectorAll(`.${formId}submit-form`);

                    prevButtons.forEach(button => {
                        button.style.display = currentStep > 0 ? \'inline-block\' : \'none\';
                    });

                    nextButtons.forEach(button => {
                        button.style.display = currentStep < totalSteps - 1 ? \'inline-block\' : \'none\';
                    });

                    submitButtons.forEach(button => {
                        button.style.display = currentStep === totalSteps - 1 ? \'inline-block\' : \'none\';
                    });
                }

                function validateCurrentStep() {
                    const currentStepElement = document.getElementById(formId + \'_step_\' + currentStep);
                    const alertElement = document.getElementById(`alert_${formId}_step_${currentStep}`);
                    let isValid = true;

                    if (!currentStepElement) return true;

                    const requiredFields = currentStepElement.querySelectorAll(\'input[required], textarea[required], select[required]\');

                    requiredFields.forEach(field => {
                        // Réinitialiser les styles d\'erreur
                        field.classList.remove(\'is-invalid\');
                        field.classList.remove(\'is-valid\');

                        if (!field.value.trim()) {
                            field.classList.add(\'is-invalid\');
                            alertElement.textContent = \'Veuillez remplir tous les champs obligatoires.\';
                            isValid = false;
                        } else {
                            field.classList.add(\'is-valid\');
                        }
                    });

                    return isValid;
                }

                function validateAllSteps() {
                    let allValid = true;
                    const alertElement = document.getElementById(`alert_${formId}_step_${currentStep}`);

                    for (let i = 0; i < totalSteps; i++) {
                        const stepElement = document.getElementById(formId + \'_step_\' + i);
                        if (stepElement) {
                            const requiredFields = stepElement.querySelectorAll(\'input[required], textarea[required], select[required]\');

                            requiredFields.forEach(field => {
                                field.classList.remove(\'is-invalid\');
                                field.classList.remove(\'is-valid\');

                                if (!field.value.trim()) {
                                    field.classList.add(\'is-invalid\');
                                    alertElement.textContent = \'Veuillez remplir tous les champs obligatoires.\';
                                    allValid = false;
                                } else {
                                    field.classList.add(\'is-valid\');
                                }
                            });
                        }
                    }

                    return allValid;
                }

                function goToFirstInvalidStep() {
                    for (let i = 0; i < totalSteps; i++) {
                        const stepElement = document.getElementById(formId + \'_step_\' + i);
                        if (stepElement) {
                            const invalidFields = stepElement.querySelectorAll(\'.is-invalid\');
                            if (invalidFields.length > 0) {
                                currentStep = i;
                                showStep(currentStep);
                                updateProgress();
                                updateButtons();

                                // Focus sur le premier champ invalide
                                if (invalidFields[0]) {
                                    invalidFields[0].focus();
                                }
                                break;
                            }
                        }
                    }
                }

                if(callback) {
                    callback?.(currentStep, totalSteps, formId, config);
                }

                // Initialisation
                updateButtons();
                updateProgress();
            });
        </script>';

        return $script;
    }

    private function buildAttributes($attributes)
    {
        $html = '';
        foreach ($attributes as $name => $value) {
            if ($name === 'id') continue;

            if (is_bool($value)) {
                if ($value) {
                    $html .= ' ' . $name;
                }
            } else {
                $html .= ' ' . $name . '="' . $this->escape($value) . '"';
            }
        }
        return $html;
    }

    private function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function addStep($title, $content, $description = null)
    {
        $this->steps[] = [
            'title' => $title,
            'content' => $content,
            'description' => $description
        ];
    }

    public function build()
    {
        return $this->createForm($this->steps, $this->attributes);
    }

    public function show()
    {
        echo $this->createForm($this->steps, $this->attributes);
    }

    public function getSteps()
    {
        return $this->steps;
    }

    public function callJs($callback)
    {
        $this->callJs = $callback;
    }

    public function getCountSteps()
    {
        return count($this->steps);
    }

    public function setRule($step, $rules)
    {
        $this->rules[$step] = $rules;
    }

    public function getRule($step = null)
    {
        return $step ? $this->rules[$step] : $this->rules;
    }

    private function itemConfig($search, $default = null)
    {
        $search = explode('.', $search);
        $config = $this->config;
        foreach ($search as $key => $value) {
            if (isset($config[$value])) {
                $config = $config[$value];
            } else {
                return $default;
            }
        }
        return $config;
    }
}