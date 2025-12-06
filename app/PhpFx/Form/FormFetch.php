<?php

namespace App\PhpFx\Form;

class FormFetch
{
    /**
     * Configuration par défaut
     */
    private static array $defaultConfig = [
        'name' => '',
        'label' => null,
        'source' => [],
        'map' => ['text' => 'nom', 'value' => 'id'],
        'placeHolder' => 'Type to search...',
        'required' => false,
        'perSearch' => 10,
        'isMulti' => false,
        'class' => 'form-control',
        'disabled' => false,
        'attributes' => [],
        'minSearchLength' => 0,
        'searchDelay' => 300,
        'showLabel' => true,
        'clearOnSelect' => true,
        'validateOnBlur' => true,
        'preloadData' => true,
        'errorMessage' => 'Please select a valid option',
        'noResultsMessage' => 'No results found',
        'loadingMessage' => 'Loading...',
        'maxSelections' => null, // Pour limiter le nombre de sélections en multi
        'allowCustomValues' => false, // Permettre des valeurs personnalisées
        'customValuePrefix' => 'custom_', // Préfixe pour les valeurs personnalisées
    ];

    /**
     * Génère le composant inputList
     */
    public static function inputList(array $config = []): string
    {
        $config = array_merge(self::$defaultConfig, $config);
        
        // Extraction des variables
        extract($config);
        
        // Gestion de la source de données
        if (is_string($source)) {
            $ajaxUrl = $source;
            $dataSource = [];
        } else {
            $dataSource = collect($source);
            $ajaxUrl = null;
        }
        
        // Construction des attributs HTML
        $attributesString = '';
        foreach ($attributes as $attr => $value) {
            if (is_bool($value)) {
                $attributesString .= $value ? " {$attr}" : '';
            } else {
                $attributesString .= " {$attr}=\"{$value}\"";
            }
        }
        
        $uniqueId = uniqid();
        $componentId = $name . '_' . $uniqueId;
        
        ob_start();
        ?>
        
        <?php if ($showLabel && $label): ?>
        <label for="input_<?= $componentId ?>" class="form-label">
            <?= $label ?> 
            <?php if ($required): ?>
                <span class="text-danger">*</span>
            <?php endif; ?>
        </label>
        <?php endif; ?>

        <div class="autocomplete-wrapper" data-component="autocomplete" data-name="<?= $name ?>">
            <!-- Message de chargement -->
            <div id="loading_<?= $componentId ?>" class="loading-message" style="display: none;">
                <small class="text-muted"><?= $loadingMessage ?></small>
            </div>
            
            <!-- Message d'erreur -->
            <div id="error_<?= $componentId ?>" class="error-message text-danger" style="display: none;">
                <small></small>
            </div>
            
            <!-- Message aucun résultat -->
            <div id="no_results_<?= $componentId ?>" class="no-results-message" style="display: none;">
                <small class="text-muted"><?= $noResultsMessage ?></small>
            </div>
            
            <input 
                type="text" 
                class="<?= $class ?>" 
                id="input_<?= $componentId ?>" 
                list="list_<?= $componentId ?>" 
                placeholder="<?= $placeHolder ?>"
                autocomplete="off"
                data-min-search="<?= $minSearchLength ?>"
                data-allow-custom="<?= $allowCustomValues ? 'true' : 'false' ?>"
                <?= $required && !$isMulti ? 'required' : '' ?>
                <?= $disabled ? 'disabled' : '' ?>
                <?= $attributesString ?>
            >
            
            <datalist id="list_<?= $componentId ?>">
                <?php foreach ($dataSource as $item): ?>
                    <option data-value="<?= $item[$map['value']] ?>" value="<?= $item[$map['text']] ?>"></option>
                <?php endforeach; ?>
            </datalist>

            <?php if ($isMulti): ?>
                <input type="hidden" id="hidden_<?= $componentId ?>" name="<?= $name ?>[]" value="" <?= $required ? 'required' : '' ?>>
                <div id="selected_<?= $componentId ?>" class="selected-items mt-2" ></div>
                <?php if ($maxSelections): ?>
                    <div id="max_selections_<?= $componentId ?>" class="max-selections-message" style="display: none;">
                        <small class="text-warning">Maximum <?= $maxSelections ?> selections allowed</small>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <input type="hidden" id="hidden_<?= $componentId ?>" name="<?= $name ?>" value="" <?= $required ? 'required' : '' ?>>
            <?php endif; ?>
            
            <!-- Bouton de reset -->
            <button type="button" class="btn-reset" id="reset_<?= $componentId ?>" style="display: none;" title="Clear selection">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <script>
        <?= self::inputListScript($componentId, $config) ?>
        </script>
        
        <?php
        return ob_get_clean();
    }

    /**
     * Script JavaScript pour le composant inputList
     */
    public static function inputListScript(string $componentId, array $config): string
    {
        extract($config);
        
        $ajaxUrl = is_string($source) ? $source : null;
        
        ob_start();
        ?>
        
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById("input_<?= $componentId ?>");
            const hidden = document.getElementById("hidden_<?= $componentId ?>");
            const datalist = document.getElementById("list_<?= $componentId ?>");
            const loadingDiv = document.getElementById("loading_<?= $componentId ?>");
            const errorDiv = document.getElementById("error_<?= $componentId ?>");
            const noResultsDiv = document.getElementById("no_results_<?= $componentId ?>");
            const resetBtn = document.getElementById("reset_<?= $componentId ?>");
            
            const isMulti = <?= $isMulti ? 'true' : 'false' ?>;
            const minSearchLength = <?= $minSearchLength ?>;
            const searchDelay = <?= $searchDelay ?>;
            const allowCustomValues = <?= $allowCustomValues ? 'true' : 'false' ?>;
            const maxSelections = <?= $maxSelections ?: 'null' ?>;
            
            if (!input || !hidden || !datalist) return;

            let searchTimeout;
            let isSearching = false;

            // Fonctions utilitaires
            function showMessage(element, message = null) {
                if (element) {
                    if (message && element.querySelector('small')) {
                        element.querySelector('small').textContent = message;
                    }
                    element.style.display = 'block';
                }
            }

            function hideMessage(element) {
                if (element) element.style.display = 'none';
            }

            function hideAllMessages() {
                hideMessage(loadingDiv);
                hideMessage(errorDiv);
                hideMessage(noResultsDiv);
            }

            function toggleResetButton() {
                if (resetBtn) {
                    resetBtn.style.display = (hidden.value || input.value) ? 'block' : 'none';
                }
            }

            <?php if ($isMulti): ?>
            const selectedContainer = document.getElementById("selected_<?= $componentId ?>");
            const maxSelectionsDiv = document.getElementById("max_selections_<?= $componentId ?>");
            const selectedValues = new Set();
            let selectedItems = [];

            function addSelectedItem(value, text, isCustom = false) {
                if (selectedValues.has(value)) return;
                
                if (maxSelections && selectedItems.length >= maxSelections) {
                    showMessage(maxSelectionsDiv);
                    setTimeout(() => hideMessage(maxSelectionsDiv), 3000);
                    return;
                }
                
                selectedValues.add(value);
                selectedItems.push({ 
                    value: value, 
                    text: text, 
                    isCustom: isCustom 
                });
                
                updateHiddenInput();
                renderSelectedItems();
                
                <?php if ($clearOnSelect): ?>
                input.value = '';
                <?php endif; ?>
                
                toggleResetButton();
                
                // Déclencher un événement personnalisé
                input.dispatchEvent(new CustomEvent('itemAdded', { 
                    detail: { value, text, isCustom } 
                }));
            }

            function removeSelectedItem(value) {
                selectedValues.delete(value);
                const removedItem = selectedItems.find(item => item.value === value);
                selectedItems = selectedItems.filter(item => item.value !== value);
                
                updateHiddenInput();
                renderSelectedItems();
                toggleResetButton();
                hideMessage(maxSelectionsDiv);
                
                // Déclencher un événement personnalisé
                if (removedItem) {
                    input.dispatchEvent(new CustomEvent('itemRemoved', { 
                        detail: removedItem 
                    }));
                }
            }

            function updateHiddenInput() {
                hidden.value = Array.from(selectedValues).join(',');
            }

            function renderSelectedItems() {
                if (!selectedContainer) return;
                
                selectedContainer.innerHTML = selectedItems.map(item => `
                    <span class="badge ${item.isCustom ? 'bg-info' : 'bg-secondary'} me-1 mb-1" title="${item.isCustom ? 'Custom value' : ''}">
                        ${item.text}
                        <button type="button" class="btn-close btn-close-white ms-1" 
                                onclick="removeSelectedItem_<?= $componentId ?>('${item.value}')" 
                                aria-label="Remove"></button>
                    </span>
                `).join('');
            }

            function clearAllSelections() {
                selectedValues.clear();
                selectedItems = [];
                updateHiddenInput();
                renderSelectedItems();
                input.value = '';
                toggleResetButton();
                hideMessage(maxSelectionsDiv);
            }

            // Fonction globale pour supprimer un élément
            window.removeSelectedItem_<?= $componentId ?> = removeSelectedItem;

            <?php else: ?>
            function setValue(value, text, isCustom = false) {
                hidden.value = value;
                input.value = text;
                toggleResetButton();
                
                // Déclencher un événement personnalisé
                input.dispatchEvent(new CustomEvent('valueChanged', { 
                    detail: { value, text, isCustom } 
                }));
            }

            function clearValue() {
                hidden.value = '';
                input.value = '';
                toggleResetButton();
            }
            <?php endif; ?>

            // Gestion de la recherche AJAX
            <?php if ($ajaxUrl): ?>
            function performSearch() {
                const searchTerm = input.value.trim();
                
                if (searchTerm.length < minSearchLength) {
                    datalist.innerHTML = "";
                    hideAllMessages();
                    return;
                }
                
                isSearching = true;
                showMessage(loadingDiv);
                hideMessage(errorDiv);
                hideMessage(noResultsDiv);
                
                const url = new URL("<?= $ajaxUrl ?>");
                url.searchParams.set('search', searchTerm);
                url.searchParams.set('per_page', <?= $perSearch ?>);
                
                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    isSearching = false;
                    hideMessage(loadingDiv);
                    
                    datalist.innerHTML = "";
                    
                    const items = Array.isArray(data) ? data : (data.data || []);
                    
                    if (items.length === 0) {
                        showMessage(noResultsDiv);
                    } else {
                        hideMessage(noResultsDiv);
                        
                        items.forEach(item => {
                            const option = document.createElement("option");
                            option.value = item.<?= $map['text'] ?>;
                            option.dataset.value = item.<?= $map['value'] ?>;
                            datalist.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    isSearching = false;
                    hideMessage(loadingDiv);
                    showMessage(errorDiv, 'Error loading data: ' + error.message);
                    console.error('Erreur lors de la recherche:', error);
                    datalist.innerHTML = "";
                });
            }

            // Préchargement des données
            <?php if ($preloadData): ?>
            performSearch();
            <?php endif; ?>
            
            // Événements de recherche
            input.addEventListener("input", function() {
                clearTimeout(searchTimeout);
                if (input.value.trim().length >= minSearchLength) {
                    searchTimeout = setTimeout(performSearch, searchDelay);
                } else {
                    hideAllMessages();
                    datalist.innerHTML = "";
                }
            });

            input.addEventListener("focus", function() {
                if (datalist.children.length === 0 && !isSearching) {
                    performSearch();
                }
            });
            <?php endif; ?>

            // Gestion de la sélection
            input.addEventListener("input", function() {
                const options = datalist.querySelectorAll("option");
                const selectedOption = Array.from(options).find(option => option.value === input.value);
                
                if (selectedOption && selectedOption.dataset.value) {
                    <?php if ($isMulti): ?>
                    addSelectedItem(selectedOption.dataset.value, selectedOption.value);
                    <?php else: ?>
                    setValue(selectedOption.dataset.value, selectedOption.value);
                    <?php endif; ?>
                } else if (allowCustomValues && input.value.trim()) {
                    // Gestion des valeurs personnalisées
                    const customValue = '<?= $customValuePrefix ?>' + input.value.trim();
                    <?php if ($isMulti): ?>
                    addSelectedItem(customValue, input.value.trim(), true);
                    <?php else: ?>
                    setValue(customValue, input.value.trim(), true);
                    <?php endif; ?>
                }
            });

            // Validation au blur
            <?php if ($validateOnBlur): ?>
            input.addEventListener("blur", function() {
                if (!allowCustomValues) {
                    const options = datalist.querySelectorAll("option");
                    const isValid = Array.from(options).some(option => option.value === input.value);
                    
                    if (!isValid && input.value !== "") {
                        <?php if (!$isMulti): ?>
                        clearValue();
                        <?php endif; ?>
                        showMessage(errorDiv, '<?= $errorMessage ?>');
                        setTimeout(() => hideMessage(errorDiv), 3000);
                    }
                }
            });
            <?php endif; ?>

            // Bouton de reset
            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    <?php if ($isMulti): ?>
                    clearAllSelections();
                    <?php else: ?>
                    clearValue();
                    <?php endif; ?>
                    hideAllMessages();
                    input.focus();
                });
            }

            // Initialisation
            toggleResetButton();
            
            // Support des touches clavier
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    <?php if ($isMulti): ?>
                    input.value = '';
                    <?php else: ?>
                    clearValue();
                    <?php endif; ?>
                    hideAllMessages();
                }
                
                <?php if ($isMulti): ?>
                if (e.key === 'Backspace' && input.value === '' && selectedItems.length > 0) {
                    // Supprimer le dernier élément sélectionné
                    const lastItem = selectedItems[selectedItems.length - 1];
                    removeSelectedItem(lastItem.value);
                }
                <?php endif; ?>
            });
        });
        
        <?php
        return ob_get_clean();
    }

    /**
     * CSS pour le composant FormFetch
     */
    public static function formFetchCSS(): string
    {
        return '
        <style>
        .autocomplete-wrapper {
            position: relative;
        }
        
        .autocomplete-wrapper .selected-items .badge {
            display: inline-flex;
            align-items: center;
            font-size: 0.875em;
            margin-bottom: 0.25rem;
            animation: fadeIn 0.3s ease-in;
        }

        .autocomplete-wrapper .selected-items .btn-close {
            --bs-btn-close-width: 0.5em;
            --bs-btn-close-height: 0.5em;
            font-size: 0.75em;
            margin-left: 0.25rem;
        }

        .autocomplete-wrapper input:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .autocomplete-wrapper input:disabled {
            background-color: #e9ecef;
            opacity: 1;
        }
        
        .autocomplete-wrapper .btn-reset {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
            z-index: 10;
        }
        
        .autocomplete-wrapper .btn-reset:hover {
            background-color: #e9ecef;
            color: #495057;
        }
        
        .autocomplete-wrapper .loading-message,
        .autocomplete-wrapper .error-message,
        .autocomplete-wrapper .no-results-message,
        .autocomplete-wrapper .max-selections-message {
            margin-top: 0.25rem;
            animation: fadeIn 0.3s ease-in;
        }
        
        .autocomplete-wrapper .badge.bg-info {
            position: relative;
        }
        
        .autocomplete-wrapper .badge.bg-info::before {
            content: "★";
            position: absolute;
            left: -8px;
            top: -2px;
            font-size: 0.6em;
            color: #0dcaf0;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Animation pour les éléments supprimés */
        .autocomplete-wrapper .badge.removing {
            animation: fadeOut 0.3s ease-out forwards;
        }
        
        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.8); }
        }
        
        /* Style responsive */
        @media (max-width: 576px) {
            .autocomplete-wrapper .selected-items .badge {
                font-size: 0.75em;
                margin-right: 0.25rem;
                margin-bottom: 0.25rem;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .autocomplete-wrapper input:focus {
                border-color: #4dabf7;
                box-shadow: 0 0 0 0.25rem rgba(77, 171, 247, 0.25);
            }
            
            .autocomplete-wrapper .btn-reset {
                color: #adb5bd;
            }
            
            .autocomplete-wrapper .btn-reset:hover {
                background-color: #495057;
                color: #f8f9fa;
            }
        }
        </style>';
    }
    
    /**
     * Méthode d'initialisation globale CSS (à appeler une seule fois)
     */
    public static function initCSS(): void
    {
        static $cssInitialized = false;
        
        if (!$cssInitialized) {
            echo self::formFetchCSS();
            $cssInitialized = true;
        }
    }

    /**
     * Méthode utilitaire pour créer des options à partir d'un modèle Eloquent
     */
    public static function fromModel(string $model, array $config = []): string
    {
        if (class_exists($model)) {
            $query = $model::query();
            
            // Application de filtres si définis
            if (isset($config['where'])) {
                foreach ($config['where'] as $field => $value) {
                    $query->where($field, $value);
                }
            }
            
            if (isset($config['orderBy'])) {
                $query->orderBy($config['orderBy'], $config['orderDirection'] ?? 'asc');
            }
            
            $config['source'] = $query->get()->toArray();
        }
        
        return self::inputList($config);
    }

    /**
     * Méthode utilitaire pour créer des options depuis une API
     */
    public static function fromApi(string $apiUrl, array $config = []): string
    {
        $config['source'] = $apiUrl;
        return self::inputList($config);
    }
}