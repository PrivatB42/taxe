<?php
namespace App\PhpFx\Html;

class HtmlMultiplicateg {
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
    
    public function duplicate(): string {
        $colClass = $this->getColClass();
        
        $html = '<div class="mt-3 mb-3 d-flex justify-content-between align-items-center">';
        $html .= '<h5>' . $this->title . '</h5>';
        $html .= $this->getAddButton();
        $html .= '</div>';

        $html .= '<div id="' . $this->containerId . '" class="' . ($this->useRowLayout ? 'row' : '') . '">';
        $html .= '<div class="' . $colClass . ' mb-3 item-template">';
        
        $html .= '<div class="card">';
        $html .= '<div class="card-body">';
        $html .= $this->template;
        $html .= '</div>';
        $html .= '<div class="card-footer text-end">';
        $html .= $this->getRemoveButton();
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    public function pushScript(): string {
        // Préparer le template pour l'insertion JavaScript
        $templateHtml = '<div class="' . $this->getColClass() . ' mb-3">';
        $templateHtml .= '<div class="card">';
        $templateHtml .= '<div class="card-body">';
        $templateHtml .= $this->template;
        $templateHtml .= '</div>';
        $templateHtml .= '<div class="card-footer text-end">';
        $templateHtml .= $this->getRemoveButton();
        $templateHtml .= '</div>';
        $templateHtml .= '</div>';
        $templateHtml .= '</div>';
        
        // Échapper les apostrophes et guillemets pour JavaScript
        $jsTemplate = str_replace(["'", "\n"], ["\\'", ""], $templateHtml);
        
        $script = '
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById("' . $this->containerId . '");
            const addButton = document.getElementById("addInput_' . $this->containerId . '");
            
            // Fonction pour initialiser les boutons de suppression
            function initRemoveButtons(element) {
                const removeButtons = element.querySelectorAll(".remove-btn");
                removeButtons.forEach(button => {
                    button.addEventListener("click", function() {
                        this.closest(".' . $this->getColClass() . '").remove();
                    });
                });
            }
            
            // Ajouter un nouvel élément
            addButton.addEventListener("click", function() {
                const newItem = document.createElement("div");
                newItem.innerHTML = \'' . $jsTemplate . '\';
                container.appendChild(newItem);
                initRemoveButtons(newItem);
            });
            
            // Initialiser les boutons existants
            initRemoveButtons(container);
            
            // Masquer le bouton de suppression du premier élément si c\'est le seul
            const items = container.querySelectorAll(".' . $this->getColClass() . '");
            if (items.length <= 1) {
                items.forEach(item => {
                    item.querySelector(".remove-btn").style.display = "none";
                });
            }
        });
        </script>';
        
        return $script;
    }
}
?>