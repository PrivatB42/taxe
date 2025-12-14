<?php
namespace App\PhpFx\Html;

use Illuminate\Support\Collection;

class HtmlMultiplicate {
    private string $containerId;
    private string $template;
    private array $addButton = [
        'class' => 'btn btn-primary',
        'text' => 'Ajouter',
        'icon' => 'fas fa-plus'
    ];
    private array $removeButton = [
        'class' => 'btn btn-danger',
        'text' => 'Supprimer',
        'icon' => 'fas fa-minus'
    ];
    private array $cols = [
        '1' => 'col-12',
        '2' => 'col-lg-6 col-md-6 col-sm-12',
        '3' => 'col-lg-4 col-md-6 col-sm-12',
        '4' => 'col-lg-3 col-md-4 col-sm-6',
        '5' => 'col-lg-2 col-md-3 col-sm-4 col-6',
        '6' => 'col-lg-2 col-md-3 col-sm-4 col-6',
        '#' => 'col-lg-1 col-md-2 col-sm-3 col-4'
    ];
    private string|null $colCustom = null;
    private string $col = '';
    private string $title = '';
    private bool $useRowLayout = true;
    private array $editData = [];
    private string $dataField = '';
    private bool $isEditMode = false;
    private int $counter = 0;
    
    public function __construct($containerId = 'inputContainer', $template = '') {
        $this->containerId = $containerId;
        $this->template = $template;
    }
    
    public function setContainerId($containerId): self {
        $this->containerId = $containerId;
        return $this;
    }
    
    public function setTemplate($template): self {
        $this->template = $template;
        return $this;
    }

    public function setTitle($title): self {
        $this->title = $title;
        return $this;
    }

    public function setAddButton($config): self {
        $this->addButton = array_merge($this->addButton, $config);
        return $this;
    }

    public function setRemoveButton($config): self {
        $this->removeButton = array_merge($this->removeButton, $config);
        return $this;
    }

    public function setCol(int $col): self {
        $this->col = (string)$col;
        return $this;
    }

    public function setColCustom($colCustom): self {
        $this->colCustom = $colCustom;
        return $this;
    }

    public function setUseRowLayout(bool $useRowLayout): self {
        $this->useRowLayout = $useRowLayout;
        return $this;
    }

    public function setEditData($data, string $dataField = ''): self {
        // Convertir les collections en tableaux
        if ($data instanceof Collection) {
            $this->editData = $data->toArray();
        } else {
            $this->editData = $data;
        }
        $this->dataField = $dataField;
        $this->isEditMode = !empty($this->editData);
        return $this;
    }

    private function getAddButton(): string {
        $class = $this->addButton['class'];
        $text = $this->addButton['text'];
        $icon = $this->addButton['icon'];
        return '<button type="button" id="addInput_' . $this->containerId . '" class="' . $class . '"><i class="' . $icon . '"></i> ' . $text . '</button>';
    }

    private function getRemoveButton(): string {
        $class = $this->removeButton['class'];
        $text = $this->removeButton['text'];
        $icon = $this->removeButton['icon'];
        return '<button type="button" class="' . $class . ' remove-btn"><i class="' . $icon . '"></i> ' . $text . '</button>';
    }

    public function getColClass(): string {
        if ($this->colCustom) {
            return $this->colCustom;
        } elseif ($this->col && isset($this->cols[$this->col])) {
            return $this->cols[$this->col];
        } else {
            return 'col-12';
        }
    }

    private function extractValue($item, $key) {
        // Gestion des relations imbriquées : user.profile.name
        if (str_contains($key, '.')) {
            $keys = explode('.', $key);
            $value = $item;
            
            foreach ($keys as $nestedKey) {
                if (is_array($value) && isset($value[$nestedKey])) {
                    $value = $value[$nestedKey];
                } elseif (is_object($value) && isset($value->{$nestedKey})) {
                    $value = $value->{$nestedKey};
                } else {
                    $value = null;
                    break;
                }
            }
            return $value;
        }
        
        // Accès direct
        if (is_array($item) && isset($item[$key])) {
            return $item[$key];
        } elseif (is_object($item) && isset($item->{$key})) {
            return $item->{$key};
        }
        
        return null;
    }

    private function renderTemplateWithData($data = [], $index = 0): string {
        $template = $this->template;
        
        // Remplacer **index** par l'index actuel
        $template = str_replace('**index**', $index, $template);
        
        // Remplacer les placeholders avec les données
        foreach ($data as $key => $value) {
            $placeholder = '**' . $key . '**';
            $template = str_replace($placeholder, htmlspecialchars($value ?? ''), $template);
        }
        
        // Gestion des conditions pour les selects/checkboxes
        $template = preg_replace_callback('/\*\*#(\w+(?:\.\w+)*)\s+([^*]+)\*\*/', function($matches) use ($data) {
            $field = $matches[1];
            $expectedValue = $matches[2];
            $actualValue = $this->extractValue($data, $field);
            return $actualValue == $expectedValue ? 'selected' : '';
        }, $template);

        // Gestion des conditions checked
        $template = preg_replace_callback('/\*\*checked\((\w+(?:\.\w+)*)\)\*\*/', function($matches) use ($data) {
            $field = $matches[1];
            $actualValue = $this->extractValue($data, $field);
            return $actualValue ? 'checked' : '';
        }, $template);

        return $template;
    }

    private function renderEditItems(): string {
        $html = '';
        $colClass = $this->getColClass();
        
        foreach ($this->editData as $index => $item) {
            // Convertir l'item en tableau si c'est un objet Eloquent
            if (is_object($item) && method_exists($item, 'toArray')) {
                $data = $item->toArray();
            } elseif (is_array($item)) {
                $data = $item;
            } else {
                $data = [$this->dataField => $item];
            }
            
            $html .= '<div class="' . $colClass . ' mb-3" data-index="' . $index . '">';
            $html .= '<div class="card">';
            $html .= '<div class="card-body">';
            $html .= $this->renderTemplateWithData($data, $index);
            $html .= '</div>';
            $html .= '<div class="card-footer text-end">';
            $html .= $this->getRemoveButton();
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        return $html;
    }

    private function renderEmptyTemplate(): string {
        $colClass = $this->getColClass();
        
        $html = '<div class="' . $colClass . ' mb-3 item-template">';
        $html .= '<div class="card">';
        $html .= '<div class="card-body">';
        $html .= $this->renderTemplateWithData([], 0);
        $html .= '</div>';
        $html .= '<div class="card-footer text-end">';
        $html .= $this->getRemoveButton();
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    public function duplicate(): string {
        $html = '<div class="mt-3 mb-3 d-flex justify-content-between align-items-center">';
        $html .= '<h5>' . $this->title . '</h5>';
        $html .= $this->getAddButton();
        $html .= '</div>';

        $html .= '<div id="' . $this->containerId . '" class="' . ($this->useRowLayout ? 'row' : '') . '">';
        
        if ($this->isEditMode) {
            // Mode édition avec données existantes
            $html .= $this->renderEditItems();
        } else {
            // Mode création avec template vide
            $html .= $this->renderEmptyTemplate();
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    public function pushScript(): string {
        $colClass = $this->getColClass();
        
        // Template pour nouveaux éléments
        $templateHtml = '<div class="' . $colClass . ' mb-3">';
        $templateHtml .= '<div class="card">';
        $templateHtml .= '<div class="card-body">';
        $templateHtml .= $this->template;
        $templateHtml .= '</div>';
        $templateHtml .= '<div class="card-footer text-end">';
        $templateHtml .= $this->getRemoveButton();
        $templateHtml .= '</div>';
        $templateHtml .= '</div>';
        $templateHtml .= '</div>';
        
        // Échapper pour JavaScript
        $jsTemplate = str_replace(["'", "\n"], ["\\'", ""], $templateHtml);
        
        $script = '
        <script>
        class HtmlMultiplicateEditor {
            constructor(containerId, template) {
                this.containerId = containerId;
                this.template = template;
                this.container = document.getElementById(containerId);
                this.addButton = document.getElementById("addInput_" + containerId);
                this.counter = this.container.querySelectorAll(".' . $colClass . '").length;
                this.init();
            }
            
            init() {
                this.initRemoveButtons();
                this.bindEvents();
                this.toggleRemoveButtons();
            }
            
            bindEvents() {
                if (this.addButton) {
                    this.addButton.addEventListener("click", () => this.addItem());
                }
            }
            
            addItem(data = {}) {
                const newItem = document.createElement("div");
                newItem.innerHTML = this.template;
                this.container.appendChild(newItem);
                
                // Mettre à jour les indexes dans les names
                this.updateIndexes(newItem, this.counter);
                
                // Remplir avec les données si fournies
                if (Object.keys(data).length > 0) {
                    this.fillItemWithData(newItem, data);
                }
                
                this.initRemoveButtons(newItem);
                this.toggleRemoveButtons();
                this.counter++;
                
                // Déclencher l\'événement
                this.triggerEvent("itemAdded", { element: newItem, data: data, index: this.counter - 1 });
            }
            
            updateIndexes(element, index) {
                // Mettre à jour **index** dans les names
                const inputs = element.querySelectorAll("[name]");
                inputs.forEach(input => {
                    const name = input.getAttribute("name");
                    if (name && name.includes("**index**")) {
                        input.setAttribute("name", name.replace("**index**", index));
                    }
                });
            }
            
            fillItemWithData(element, data) {
                for (const [key, value] of Object.entries(data)) {
                    const inputs = element.querySelectorAll(`[name*="${key}"]`);
                    inputs.forEach(input => {
                        if (input.type === "checkbox") {
                            input.checked = !!value;
                        } else if (input.type === "radio") {
                            input.checked = input.value == value;
                        } else {
                            input.value = value;
                        }
                    });
                    
                    // Gérer les textarea et select
                    const textareas = element.querySelectorAll(`textarea[name*="${key}"]`);
                    textareas.forEach(textarea => {
                        textarea.value = value;
                    });
                    
                    const selects = element.querySelectorAll(`select[name*="${key}"]`);
                    selects.forEach(select => {
                        select.value = value;
                    });
                }
            }
            
            removeItem(element) {
                const data = this.getItemData(element);
                const index = Array.from(this.container.children).indexOf(element);
                element.remove();
                this.toggleRemoveButtons();
                this.triggerEvent("itemRemoved", { element: element, data: data, index: index });
            }
            
            getItemData(element) {
                const data = {};
                const inputs = element.querySelectorAll("input, textarea, select");
                
                inputs.forEach(input => {
                    if (input.name) {
                        const cleanName = input.name.replace(/\[\d+\]/, "").replace("[]", "");
                        if (input.type === "checkbox") {
                            data[cleanName] = input.checked;
                        } else if (input.type === "radio") {
                            if (input.checked) {
                                data[cleanName] = input.value;
                            }
                        } else {
                            data[cleanName] = input.value;
                        }
                    }
                });
                
                return data;
            }
            
            getAllData() {
                const items = this.container.querySelectorAll(".' . $colClass . '");
                const data = [];
                
                items.forEach((item, index) => {
                    if (!item.classList.contains("item-template")) {
                        data.push(this.getItemData(item));
                    }
                });
                
                return data;
            }
            
            getDataAsFormData() {
                const formData = new FormData();
                const allData = this.getAllData();
                
                allData.forEach((item, index) => {
                    Object.entries(item).forEach(([key, value]) => {
                        if (Array.isArray(value)) {
                            value.forEach(v => formData.append(`${key}[${index}]`, v));
                        } else {
                            formData.append(`${key}[${index}]`, value);
                        }
                    });
                });
                
                return formData;
            }
            
            initRemoveButtons(parent = this.container) {
                const removeButtons = parent.querySelectorAll(".remove-btn");
                removeButtons.forEach(button => {
                    button.addEventListener("click", (e) => {
                        e.preventDefault();
                        const item = button.closest(".' . $colClass . '");
                        this.removeItem(item);
                    });
                });
            }
            
            toggleRemoveButtons() {
                const items = this.container.querySelectorAll(".' . $colClass . '");
                const visibleItems = Array.from(items).filter(item => 
                    !item.classList.contains("item-template") && 
                    item.style.display !== "none"
                );
                
                // Masquer le bouton de suppression s\'il n\'y a qu\'un seul élément
                if (visibleItems.length <= 1) {
                    visibleItems.forEach(item => {
                        const btn = item.querySelector(".remove-btn");
                        if (btn) btn.style.display = "none";
                    });
                } else {
                    visibleItems.forEach(item => {
                        const btn = item.querySelector(".remove-btn");
                        if (btn) btn.style.display = "";
                    });
                }
            }
            
            triggerEvent(eventName, detail) {
                const event = new CustomEvent("htmlMultiplicate:" + eventName, {
                    detail: detail,
                    bubbles: true
                });
                this.container.dispatchEvent(event);
            }
            
            // API pour l\'édition externe
            loadData(dataArray) {
                this.clear();
                dataArray.forEach(data => this.addItem(data));
            }
            
            clear() {
                const items = this.container.querySelectorAll(".' . $colClass . '");
                items.forEach(item => {
                    if (!item.classList.contains("item-template")) {
                        item.remove();
                    }
                });
                this.counter = 0;
                this.toggleRemoveButtons();
            }
            
            on(eventName, callback) {
                this.container.addEventListener("htmlMultiplicate:" + eventName, callback);
            }
        }

        // Initialisation
        document.addEventListener("DOMContentLoaded", function() {
            const editor = new HtmlMultiplicateEditor("' . $this->containerId . '", \'' . $jsTemplate . '\');
            
            // Exposer l\'éditeur globalement pour un usage externe
            if (!window.htmlMultiplicateEditors) {
                window.htmlMultiplicateEditors = {};
            }
            window.htmlMultiplicateEditors["' . $this->containerId . '"] = editor;
        });
        </script>';
        
        return $script;
    }
}
?>