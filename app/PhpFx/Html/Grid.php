<?php

namespace PhpFx\Html;

class Grid
{
    private $elements = [];
    private $elementsPerRow = 12;
    private $gutterX = 3;
    private $gutterY = 3;
    private $containerClass = 'container-fluid';
    private $rowClass = 'row';
    private $containerAttributes = [];
    private $rowAttributes = [];
    
    // Configuration responsive avancée
    private $responsive = [
        'xs' => null,
        'sm' => null,
        'md' => null,
        'lg' => null,
        'xl' => null,
        'xxl' => null
    ];
    
    // Alignement et justification
    private $alignment = [
        'horizontal' => null,
        'vertical' => null
    ];
    
    // Configuration avancée des colonnes
    private $columnConfig = [
        'autoSize' => false,
        'equalHeight' => false,
        'noGutters' => false
    ];
    
    // Animation et effets
    private $animations = [
        'enabled' => false,
        'type' => 'fade',
        'delay' => 0,
        'duration' => 300
    ];
    
    // Système de tri et filtrage
    private $sorting = [
        'enabled' => false,
        'key' => null,
        'direction' => 'asc',
        'comparator' => null
    ];
    
    private $filtering = [
        'enabled' => false,
        'filters' => []
    ];
    
    // Templates et thèmes
    private $templates = [];
    private $theme = 'default';
    
    // Callbacks et events
    private $callbacks = [
        'beforeRender' => null,
        'afterRender' => null,
        'onElementAdd' => null
    ];
    
    // Système de cache
    private $cache = [
        'enabled' => false,
        'key' => null,
        'ttl' => 3600
    ];
    
    // Modes d'affichage
    private $displayModes = [
        'masonry' => false,
        'carousel' => false,
        'infinite' => false,
        'pagination' => false
    ];
    
    // Configuration de pagination
    private $pagination = [
        'itemsPerPage' => 12,
        'currentPage' => 1,
        'showControls' => true,
        'showNumbers' => true
    ];
    
    // Breakpoints personnalisés
    private $customBreakpoints = [];
    
    // Système de slots/zones
    private $slots = [];
    private $currentSlot = 'default';

    // Métriques et performance
    private $renderTime = 0;
    private $cacheHits = 0;
    private $cacheMisses = 0;
    private $savedStates = [];

    // Plugins
    private $plugins = [];

    /**
     * Constructeur avec configuration initiale
     */
    public function __construct($config = [])
    {
        $this->applyConfig($config);
        $this->initializeDefaultTemplates();
    }

    /**
     * Application de configuration en masse
     */
    public function applyConfig($config)
    {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                if (is_array($this->$key)) {
                    $this->$key = array_merge($this->$key, $value);
                } else {
                    $this->$key = $value;
                }
            }
        }
        return $this;
    }

    /**
     * Configuration du nombre d'éléments par ligne
     */
    public function setElementsPerRow($count)
    {
        $this->elementsPerRow = max(1, min(12, $count));
        return $this;
    }

    /**
     * Configuration des gouttières
     */
    public function setGutter($x, $y = null)
    {
        $this->gutterX = $x;
        $this->gutterY = $y ?? $x;
        return $this;
    }

    /**
     * Configuration du conteneur avec options avancées
     */
    public function setContainer($type = 'fluid', $attributes = [], $classes = [])
    {
        $this->containerClass = $type === 'fixed' ? 'container' : 'container-fluid';
        
        if (!empty($classes)) {
            $this->containerClass .= ' ' . implode(' ', $classes);
        }
        
        $this->containerAttributes = $attributes;
        return $this;
    }

    /**
     * Configuration de la row avec classes personnalisées
     */
    public function setRow($classes = [], $attributes = [])
    {
        if (!empty($classes)) {
            $this->rowClass = 'row ' . implode(' ', $classes);
        }
        $this->rowAttributes = $attributes;
        return $this;
    }

    /**
     * Configuration responsive avancée avec breakpoints personnalisés
     */
    public function setResponsive($breakpoint, $elementsPerRow, $options = [])
    {
        if (array_key_exists($breakpoint, $this->responsive)) {
            $this->responsive[$breakpoint] = [
                'elementsPerRow' => max(1, min(12, $elementsPerRow)),
                'gutter' => $options['gutter'] ?? null,
                'alignment' => $options['alignment'] ?? null,
                'display' => $options['display'] ?? 'block'
            ];
        } else {
            // Breakpoint personnalisé
            $this->customBreakpoints[$breakpoint] = [
                'minWidth' => $options['minWidth'] ?? null,
                'maxWidth' => $options['maxWidth'] ?? null,
                'elementsPerRow' => $elementsPerRow,
                'gutter' => $options['gutter'] ?? null
            ];
        }
        return $this;
    }

    /**
     * Configuration des colonnes avec options avancées
     */
    public function setColumnConfig($autoSize = false, $equalHeight = false, $noGutters = false)
    {
        $this->columnConfig = [
            'autoSize' => $autoSize,
            'equalHeight' => $equalHeight,
            'noGutters' => $noGutters
        ];
        return $this;
    }

    /**
     * Configuration des animations
     */
    public function setAnimations($enabled = true, $type = 'fade', $delay = 0, $duration = 300)
    {
        $this->animations = [
            'enabled' => $enabled,
            'type' => $type,
            'delay' => $delay,
            'duration' => $duration
        ];
        return $this;
    }

    /**
     * Active le mode masonry
     */
    public function enableMasonry($options = [])
    {
        $this->displayModes['masonry'] = true;
        return $this;
    }

    /**
     * Active le mode carousel
     */
    public function enableCarousel($options = [])
    {
        $this->displayModes['carousel'] = array_merge([
            'autoSlide' => false,
            'interval' => 5000,
            'indicators' => true,
            'controls' => true
        ], $options);
        return $this;
    }

    /**
     * Configuration de la pagination
     */
    public function setPagination($itemsPerPage = 12, $showControls = true, $showNumbers = true)
    {
        $this->displayModes['pagination'] = true;
        $this->pagination = [
            'itemsPerPage' => $itemsPerPage,
            'currentPage' => 1,
            'showControls' => $showControls,
            'showNumbers' => $showNumbers
        ];
        return $this;
    }

    /**
     * Système de tri avancé
     */
    public function enableSorting($key, $direction = 'asc', $customComparator = null)
    {
        $this->sorting = [
            'enabled' => true,
            'key' => $key,
            'direction' => $direction,
            'comparator' => $customComparator
        ];
        return $this;
    }

    /**
     * Système de filtrage avancé
     */
    public function enableFiltering($filters = [])
    {
        $this->filtering = [
            'enabled' => true,
            'filters' => $filters
        ];
        return $this;
    }

    /**
     * Définition de templates personnalisés
     */
    public function setTemplate($name, $template)
    {
        $this->templates[$name] = $template;
        return $this;
    }

    /**
     * Application d'un thème
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * Système de slots/zones
     */
    public function setSlot($name)
    {
        $this->currentSlot = $name;
        if (!isset($this->slots[$name])) {
            $this->slots[$name] = [];
        }
        return $this;
    }

    /**
     * Ajout d'élément avec options très avancées
     */
    public function addElement($content, $options = [])
    {
        $element = [
            'content' => $content,
            'class' => $options['class'] ?? '',
            'id' => $options['id'] ?? 'grid-item-' . uniqid(),
            'attributes' => $options['attributes'] ?? [],
            'colSize' => $options['colSize'] ?? null,
            'responsive' => $options['responsive'] ?? [],
            'order' => $options['order'] ?? null,
            'offset' => $options['offset'] ?? null,
            'priority' => $options['priority'] ?? 0,
            'tags' => $options['tags'] ?? [],
            'data' => $options['data'] ?? [],
            'template' => $options['template'] ?? null,
            'slot' => $options['slot'] ?? $this->currentSlot,
            'visibility' => $options['visibility'] ?? [],
            'animation' => $options['animation'] ?? null,
            'tooltip' => $options['tooltip'] ?? null,
            'modal' => $options['modal'] ?? null,
            'sortValue' => $options['sortValue'] ?? null,
            'filterValues' => $options['filterValues'] ?? [],
            'lazy' => $options['lazy'] ?? false,
            'skeleton' => $options['skeleton'] ?? null
        ];
        
        // Callback d'ajout d'élément
        if ($this->callbacks['onElementAdd'] && is_callable($this->callbacks['onElementAdd'])) {
            $element = call_user_func($this->callbacks['onElementAdd'], $element);
        }
        
        $this->elements[] = $element;
        return $this;
    }

    /**
     * Ajout d'éléments avec template
     */
    public function addElementsFromData($data, $template, $options = [])
    {
        foreach ($data as $item) {
            $content = $this->renderTemplate($template, $item);
            $this->addElement($content, array_merge($options, ['data' => $item]));
        }
        return $this;
    }

    /**
     * Rendu de template
     */
    private function renderTemplate($template, $data)
    {
        // Template simple avec remplacement de variables
        $content = $template;
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $content = str_replace('{{' . $key . '}}', $value, $content);
            }
        }
        return $content;
    }

    /**
     * Callbacks pour les events
     */
    public function onBeforeRender($callback)
    {
        $this->callbacks['beforeRender'] = $callback;
        return $this;
    }

    public function onAfterRender($callback)
    {
        $this->callbacks['afterRender'] = $callback;
        return $this;
    }

    public function onElementAdd($callback)
    {
        $this->callbacks['onElementAdd'] = $callback;
        return $this;
    }

    /**
     * Système de cache
     */
    public function enableCache($key = null, $ttl = 3600)
    {
        $this->cache = [
            'enabled' => true,
            'key' => $key ?? 'grid_' . md5(serialize($this->elements)),
            'ttl' => $ttl
        ];
        return $this;
    }

    /**
     * Application des filtres
     */
    private function applyFilters()
    {
        if (!$this->filtering['enabled']) {
            return $this->elements;
        }

        $filtered = $this->elements;
        foreach ($this->filtering['filters'] as $filter) {
            $filtered = array_filter($filtered, function($element) use ($filter) {
                return in_array($filter, $element['filterValues']);
            });
        }
        return $filtered;
    }

    /**
     * Application du tri
     */
    private function applySorting($elements)
    {
        if (!$this->sorting['enabled']) {
            return $elements;
        }

        if ($this->sorting['comparator'] && is_callable($this->sorting['comparator'])) {
            usort($elements, $this->sorting['comparator']);
        } else {
            usort($elements, function($a, $b) {
                $aVal = $a['sortValue'] ?? $a[$this->sorting['key']] ?? 0;
                $bVal = $b['sortValue'] ?? $b[$this->sorting['key']] ?? 0;
                
                if ($this->sorting['direction'] === 'asc') {
                    return $aVal <=> $bVal;
                } else {
                    return $bVal <=> $aVal;
                }
            });
        }
        
        return $elements;
    }

    /**
     * Application de la pagination
     */
    private function applyPagination($elements)
    {
        if (!$this->displayModes['pagination']) {
            return $elements;
        }

        $offset = ($this->pagination['currentPage'] - 1) * $this->pagination['itemsPerPage'];
        return array_slice($elements, $offset, $this->pagination['itemsPerPage']);
    }

    /**
     * Génération des contrôles de pagination
     */
    private function renderPaginationControls($totalItems)
    {
        if (!$this->displayModes['pagination'] || !$this->pagination['showControls']) {
            return '';
        }

        $totalPages = ceil($totalItems / $this->pagination['itemsPerPage']);
        $currentPage = $this->pagination['currentPage'];

        $html = '<nav aria-label="Grid pagination" class="d-flex justify-content-center mt-4">';
        $html .= '<ul class="pagination">';

        // Bouton précédent
        $disabled = $currentPage <= 1 ? 'disabled' : '';
        $html .= '<li class="page-item ' . $disabled . '">';
        $html .= '<a class="page-link" href="#" data-page="' . ($currentPage - 1) . '">Précédent</a>';
        $html .= '</li>';

        // Numéros de page
        if ($this->pagination['showNumbers']) {
            for ($i = 1; $i <= $totalPages; $i++) {
                $active = $i === $currentPage ? 'active' : '';
                $html .= '<li class="page-item ' . $active . '">';
                $html .= '<a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a>';
                $html .= '</li>';
            }
        }

        // Bouton suivant
        $disabled = $currentPage >= $totalPages ? 'disabled' : '';
        $html .= '<li class="page-item ' . $disabled . '">';
        $html .= '<a class="page-link" href="#" data-page="' . ($currentPage + 1) . '">Suivant</a>';
        $html .= '</li>';

        $html .= '</ul></nav>';
        return $html;
    }

    /**
     * Génération du JavaScript pour les fonctionnalités avancées
     */
    private function generateJavaScript()
    {
        $js = '<script>';
        
        // Animation
        if ($this->animations['enabled']) {
            $js .= "
            document.addEventListener('DOMContentLoaded', function() {
                const items = document.querySelectorAll('.grid-item');
                items.forEach((item, index) => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        item.style.transition = 'all {$this->animations['duration']}ms ease';
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    }, index * {$this->animations['delay']} + 100);
                });
            });";
        }

        // Masonry
        if ($this->displayModes['masonry']) {
            $js .= "
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.querySelector('.masonry-container');
                if (container) {
                    const items = container.querySelectorAll('.col');
                    // Logique masonry basique
                    let columns = [];
                    const numColumns = Math.floor(container.offsetWidth / 300);
                    for (let i = 0; i < numColumns; i++) columns[i] = 0;
                    
                    items.forEach(item => {
                        const shortestColumn = columns.indexOf(Math.min(...columns));
                        item.style.order = shortestColumn;
                        columns[shortestColumn] += item.offsetHeight;
                    });
                }
            });";
        }

        // Pagination
        if ($this->displayModes['pagination']) {
            $js .= "
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('page-link')) {
                    e.preventDefault();
                    const page = parseInt(e.target.dataset.page);
                    // Ici vous pourriez déclencher un rechargement AJAX
                    console.log('Changement de page:', page);
                }
            });";
        }

        // Lazy loading
        $js .= "
        if (typeof IntersectionObserver !== 'undefined') {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const item = entry.target;
                        if (item.dataset.lazy) {
                            item.innerHTML = item.dataset.lazy;
                            item.removeAttribute('data-lazy');
                            observer.unobserve(item);
                        }
                    }
                });
            });
            
            document.querySelectorAll('[data-lazy]').forEach(item => {
                observer.observe(item);
            });
        }";

        $js .= '</script>';
        return $js;
    }

    /**
     * Initialisation des templates par défaut
     */
    private function initializeDefaultTemplates()
    {
        $this->templates['card'] = '
        <div class="card h-100">
            <img src="{{image}}" class="card-img-top" alt="{{title}}">
            <div class="card-body">
                <h5 class="card-title">{{title}}</h5>
                <p class="card-text">{{description}}</p>
                <a href="{{link}}" class="btn btn-primary">{{buttonText}}</a>
            </div>
        </div>';

        $this->templates['list-item'] = '
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-1">{{title}}</h6>
                <p class="mb-1">{{description}}</p>
            </div>
            <span class="badge bg-primary rounded-pill">{{badge}}</span>
        </div>';
    }

    /**
     * Presets ultra avancés
     */
    public function preset($preset, $options = [])
    {
        switch ($preset) {
            case 'portfolio':
                $this->setElementsPerRow(3)
                     ->setResponsive('lg', 3)
                     ->setResponsive('md', 2)
                     ->setResponsive('sm', 1)
                     ->setGutter(4)
                     ->enableMasonry()
                     ->setAnimations(true, 'fade', 100, 400);
                break;
                
            case 'blog':
                $this->setElementsPerRow(2)
                     ->setResponsive('md', 1)
                     ->setGutter(5, 4)
                     ->setPagination(6)
                     ->setAnimations(true, 'slide', 50, 300);
                break;
                
            case 'ecommerce':
                $this->setElementsPerRow(4)
                     ->setResponsive('lg', 3)
                     ->setResponsive('md', 2)
                     ->setResponsive('sm', 1)
                     ->setGutter(3)
                     ->enableSorting('price')
                     ->enableFiltering()
                     ->setPagination(12);
                break;
                
            case 'dashboard':
                $this->setElementsPerRow(6)
                     ->setResponsive('xl', 4)
                     ->setResponsive('lg', 3)
                     ->setResponsive('md', 2)
                     ->setResponsive('sm', 1)
                     ->setGutter(2)
                     ->setColumnConfig(true, true);
                break;

            case 'news':
                $this->setElementsPerRow(1)
                     ->setGutter(4, 3)
                     ->enableSorting('date', 'desc')
                     ->setPagination(10)
                     ->setAnimations(true, 'fade', 75, 350);
                break;
        }
        
        return $this;
    }

    /**
     * Rendu principal ultra avancé
     */
    public function render($return = false)
    {
        $startTime = microtime(true);
        
        // Callback avant rendu
        if ($this->callbacks['beforeRender'] && is_callable($this->callbacks['beforeRender'])) {
            call_user_func($this->callbacks['beforeRender'], $this);
        }

        // Vérification du cache
        if ($this->cache['enabled']) {
            $cached = $this->getCachedRender();
            if ($cached) {
                $this->cacheHits++;
                $this->renderTime = microtime(true) - $startTime;
                return $return ? $cached : print($cached);
            }
            $this->cacheMisses++;
        }

        // Traitement des éléments
        $elements = $this->elements;
        $elements = $this->applyFilters();
        $elements = $this->applySorting($elements);
        
        $totalItems = count($elements);
        $elements = $this->applyPagination($elements);

        // Classes du conteneur
        $containerClasses = $this->containerClass;
        if ($this->displayModes['masonry']) {
            $containerClasses .= ' masonry-container';
        }

        // Génération HTML
        $html = $this->generateHTML($elements, $containerClasses, $totalItems);

        // Mise en cache
        if ($this->cache['enabled']) {
            $this->setCachedRender($html);
        }

        // Callback après rendu
        if ($this->callbacks['afterRender'] && is_callable($this->callbacks['afterRender'])) {
            $html = call_user_func($this->callbacks['afterRender'], $this, $html);
        }

        $this->renderTime = microtime(true) - $startTime;

        return $return ? $html : print($html);
    }

    /**
     * Génération du HTML complet
     */
    private function generateHTML($elements, $containerClasses, $totalItems)
    {
        $containerAttrs = $this->generateAttributes($this->containerAttributes);
        $rowAttrs = $this->generateAttributes($this->rowAttributes);
        
        $html = '<div class="' . $containerClasses . '"' . ($containerAttrs ? ' ' . $containerAttrs : '') . '>' . "\n";
        
        // Carousel wrapper si activé
        if ($this->displayModes['carousel']) {
            $html .= $this->generateCarouselWrapper($elements);
        } else {
            $html .= '  <div class="' . $this->getRowClasses() . '"' . ($rowAttrs ? ' ' . $rowAttrs : '') . '>' . "\n";
            
            foreach ($elements as $index => $element) {
                $html .= $this->renderElement($element, $index);
            }
            
            $html .= '  </div>' . "\n";
        }

        // Contrôles de pagination
        if ($this->displayModes['pagination']) {
            $html .= $this->renderPaginationControls($totalItems);
        }

        $html .= '</div>' . "\n";

        // JavaScript
        $html .= $this->generateJavaScript();

        return $html;
    }

    /**
     * Rendu d'un élément individuel
     */
    private function renderElement($element, $index)
    {
        $colClass = $this->getColumnClass($element);
        $elementClass = $element['class'] ? ' ' . $element['class'] : '';
        $elementClass .= ' grid-item';
        
        if ($element['lazy']) {
            $elementClass .= ' lazy-item';
        }
        
        $elementId = $element['id'] ? ' id="' . $element['id'] . '"' : '';
        $elementAttrs = $this->generateAttributes($element['attributes']);
        $elementAttrs .= $element['lazy'] ? ' data-lazy="' . htmlspecialchars($element['content']) . '"' : '';
        $elementAttrs .= $element['tooltip'] ? ' title="' . htmlspecialchars($element['tooltip']) . '"' : '';
        $elementAttrs = $elementAttrs ? ' ' . $elementAttrs : '';

        $html = '    <div class="' . $colClass . $elementClass . '"' . $elementId . $elementAttrs . '>' . "\n";
        
        if ($element['lazy'] && $element['skeleton']) {
            $html .= '      ' . $element['skeleton'] . "\n";
        } else {
            $html .= '      ' . $element['content'] . "\n";
        }
        
        $html .= '    </div>' . "\n";

        return $html;
    }

    /**
     * Calcul avancé des classes de colonne
     */
    private function getColumnClass($element)
    {
        $classes = [];
        
        // Auto-sizing
        if ($this->columnConfig['autoSize'] && !$element['colSize']) {
            $classes[] = 'col';
        } else {
            if ($element['colSize']) {
                $classes[] = 'col-' . $element['colSize'];
            } else {
                $colSize = 12 / $this->elementsPerRow;
                $classes[] = 'col-' . intval($colSize);
            }
        }

        // Classes responsive
        foreach ($this->responsive as $breakpoint => $config) {
            if (is_array($config) && isset($config['elementsPerRow']) && $config['elementsPerRow']) {
                $colSize = 12 / $config['elementsPerRow'];
                $classes[] = 'col-' . $breakpoint . '-' . intval($colSize);
            } elseif (is_numeric($config)) {
                $colSize = 12 / $config;
                $classes[] = 'col-' . $breakpoint . '-' . intval($colSize);
            }
        }

        // Responsive personnalisé pour l'élément
        if (!empty($element['responsive'])) {
            foreach ($element['responsive'] as $breakpoint => $size) {
                $classes[] = 'col-' . $breakpoint . '-' . $size;
            }
        }

        // Equal height
        if ($this->columnConfig['equalHeight']) {
            $classes[] = 'd-flex';
        }

        // Ordre et offset
        if ($element['order'] !== null) {
            $classes[] = 'order-' . $element['order'];
        }
        if ($element['offset'] !== null) {
            $classes[] = 'offset-' . $element['offset'];
        }

        // Visibilité responsive
        foreach ($element['visibility'] as $breakpoint => $visible) {
            if (!$visible) {
                $classes[] = 'd-' . $breakpoint . '-none';
            }
        }

        return implode(' ', $classes);
    }

    /**
     * Classes avancées de la row
     */
    private function getRowClasses()
    {
        $classes = [$this->rowClass];

        // No gutters
        if ($this->columnConfig['noGutters']) {
            $classes[] = 'g-0';
        } else {
            // Gutter personnalisé
            if ($this->gutterX !== 3 || $this->gutterY !== 3) {
                if ($this->gutterX === $this->gutterY) {
                    $classes[] = 'g-' . $this->gutterX;
                } else {
                    $classes[] = 'gx-' . $this->gutterX;
                    $classes[] = 'gy-' . $this->gutterY;
                }
            }
        }

        // Alignements
        if ($this->alignment['horizontal']) {
            $classes[] = 'justify-content-' . $this->alignment['horizontal'];
        }
        if ($this->alignment['vertical']) {
            $classes[] = 'align-items-' . $this->alignment['vertical'];
        }

        // Equal height
        if ($this->columnConfig['equalHeight']) {
            $classes[] = 'align-items-stretch';
        }

        return implode(' ', $classes);
    }

    /**
     * Génération d'attributs HTML
     */
    private function generateAttributes($attributes)
    {
        $attrs = [];
        foreach ($attributes as $key => $value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }
            $attrs[] = $key . '="' . htmlspecialchars($value) . '"';
        }
        return implode(' ', $attrs);
    }

    /**
     * Méthodes de cache
     */
    private function getCachedRender()
    {
        // Implémentation basique du cache (à adapter selon votre système)
        // Pour l'exemple, on retourne toujours false
        return false;
    }

    private function setCachedRender($html)
    {
        // Sauvegarder dans le cache
        // À implémenter selon votre système de cache
    }

    /**
     * Génération du carousel wrapper
     */
    private function generateCarouselWrapper($elements)
    {
        $carouselId = 'carousel-' . uniqid();
        $html = '<div id="' . $carouselId . '" class="carousel slide" data-bs-ride="carousel">';
        
        // Indicators
        if ($this->displayModes['carousel']['indicators']) {
            $html .= '<div class="carousel-indicators">';
            for ($i = 0; $i < count($elements); $i++) {
                $active = $i === 0 ? 'active' : '';
                $html .= '<button type="button" data-bs-target="#' . $carouselId . '" data-bs-slide-to="' . $i . '" class="' . $active . '"></button>';
            }
            $html .= '</div>';
        }
        
        // Carousel inner
        $html .= '<div class="carousel-inner">';
        foreach ($elements as $index => $element) {
            $active = $index === 0 ? 'active' : '';
            $html .= '<div class="carousel-item ' . $active . '">';
            $html .= $element['content'];
            $html .= '</div>';
        }
        $html .= '</div>';
        
        // Controls
        if ($this->displayModes['carousel']['controls']) {
            $html .= '<button class="carousel-control-prev" type="button" data-bs-target="#' . $carouselId . '" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#' . $carouselId . '" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>';
        }
        
        $html .= '</div>';
        return $html;
    }

    /**
     * Export de la grille vers différents formats
     */
    public function export($format = 'json')
    {
        $data = [
            'elements' => $this->elements,
            'config' => [
                'elementsPerRow' => $this->elementsPerRow,
                'gutter' => ['x' => $this->gutterX, 'y' => $this->gutterY],
                'responsive' => $this->responsive,
                'alignment' => $this->alignment,
                'theme' => $this->theme,
                'displayModes' => $this->displayModes
            ]
        ];

        switch ($format) {
            case 'json':
                return json_encode($data, JSON_PRETTY_PRINT);
            case 'xml':
                return $this->arrayToXml($data);
            case 'csv':
                return $this->arrayToCsv($data['elements']);
            default:
                return $data;
        }
    }

    /**
     * Import de configuration depuis différents formats
     */
    public function import($data, $format = 'json')
    {
        switch ($format) {
            case 'json':
                $decoded = json_decode($data, true);
                break;
            case 'xml':
                $decoded = $this->xmlToArray($data);
                break;
            default:
                $decoded = $data;
        }

        if (isset($decoded['elements'])) {
            $this->elements = $decoded['elements'];
        }
        if (isset($decoded['config'])) {
            $this->applyConfig($decoded['config']);
        }

        return $this;
    }

    /**
     * Conversion XML vers tableau
     */
    private function xmlToArray($xmlString)
    {
        $xml = simplexml_load_string($xmlString);
        $json = json_encode($xml);
        return json_decode($json, true);
    }

    /**
     * Système de recherche avancée
     */
    public function search($query, $fields = ['content'])
    {
        $results = array_filter($this->elements, function($element) use ($query, $fields) {
            foreach ($fields as $field) {
                $value = $this->getNestedValue($element, $field);
                if (stripos($value, $query) !== false) {
                    return true;
                }
            }
            return false;
        });

        // Créer une nouvelle instance avec les résultats
        $grid = new Grid();
        $grid->elements = array_values($results);
        $grid->copyConfigFrom($this);
        return $grid;
    }

    /**
     * Copie la configuration d'une autre grille
     */
    private function copyConfigFrom($otherGrid)
    {
        $this->elementsPerRow = $otherGrid->elementsPerRow;
        $this->gutterX = $otherGrid->gutterX;
        $this->gutterY = $otherGrid->gutterY;
        $this->responsive = $otherGrid->responsive;
        $this->alignment = $otherGrid->alignment;
        $this->theme = $otherGrid->theme;
    }

    /**
     * Groupe les éléments par critère
     */
    public function groupBy($field)
    {
        $groups = [];
        foreach ($this->elements as $element) {
            $value = $this->getNestedValue($element, $field);
            if (!isset($groups[$value])) {
                $groups[$value] = [];
            }
            $groups[$value][] = $element;
        }
        return $groups;
    }

    /**
     * Récupère une valeur imbriquée dans un tableau
     */
    private function getNestedValue($array, $path)
    {
        $keys = explode('.', $path);
        $value = $array;
        
        foreach ($keys as $key) {
            if (is_array($value) && isset($value[$key])) {
                $value = $value[$key];
            } else {
                return '';
            }
        }
        
        return is_string($value) ? $value : '';
    }

    /**
     * Système de métriques et analytics
     */
    public function getMetrics()
    {
        return [
            'totalElements' => count($this->elements),
            'averageContentLength' => $this->getAverageContentLength(),
            'elementsBySlot' => $this->getElementCountBySlot(),
            'responsiveBreakpoints' => array_keys(array_filter($this->responsive)),
            'renderTime' => $this->getLastRenderTime(),
            'cacheHitRate' => $this->getCacheHitRate()
        ];
    }

    private function getAverageContentLength()
    {
        if (empty($this->elements)) return 0;
        
        $totalLength = array_sum(array_map(function($element) {
            return strlen(strip_tags($element['content']));
        }, $this->elements));
        
        return round($totalLength / count($this->elements), 2);
    }

    private function getElementCountBySlot()
    {
        $counts = [];
        foreach ($this->elements as $element) {
            $slot = $element['slot'] ?? 'default';
            $counts[$slot] = ($counts[$slot] ?? 0) + 1;
        }
        return $counts;
    }

    private function getLastRenderTime()
    {
        return $this->renderTime;
    }

    private function getCacheHitRate()
    {
        $total = $this->cacheHits + $this->cacheMisses;
        return $total > 0 ? round(($this->cacheHits / $total) * 100, 2) : 0;
    }

    /**
     * Système de validation des éléments
     */
    public function validate()
    {
        $errors = [];
        
        foreach ($this->elements as $index => $element) {
            // Validation du contenu
            if (empty($element['content'])) {
                $errors[] = "Élément $index: contenu vide";
            }
            
            // Validation des classes CSS
            if (isset($element['class']) && !$this->isValidCssClass($element['class'])) {
                $errors[] = "Élément $index: classe CSS invalide";
            }
            
            // Validation des tailles de colonnes
            if (isset($element['colSize']) && ($element['colSize'] < 1 || $element['colSize'] > 12)) {
                $errors[] = "Élément $index: taille de colonne invalide (1-12)";
            }
        }
        
        return $errors;
    }

    private function isValidCssClass($class)
    {
        return preg_match('/^[a-zA-Z][-\w]*$/', $class);
    }

    /**
     * Système de thèmes avancé
     */
    public function loadTheme($themeName)
    {
        $themes = [
            'dark' => [
                'containerClass' => 'container-fluid bg-dark text-light',
                'rowClass' => 'row',
                'cardClass' => 'card bg-secondary text-light'
            ],
            'minimal' => [
                'containerClass' => 'container-fluid',
                'rowClass' => 'row g-1',
                'cardClass' => 'card border-0 shadow-sm'
            ],
            'colorful' => [
                'containerClass' => 'container-fluid bg-gradient',
                'rowClass' => 'row g-4',
                'cardClass' => 'card border-primary'
            ]
        ];

        if (isset($themes[$themeName])) {
            $theme = $themes[$themeName];
            $this->containerClass = $theme['containerClass'];
            $this->rowClass = $theme['rowClass'];
            $this->theme = $themeName;
        }

        return $this;
    }

    /**
     * Générateur de CSS personnalisé pour des styles avancés
     */
    public function generateCustomCSS()
    {
        $css = '<style>';
        
        // Animations personnalisées
        if ($this->animations['enabled']) {
            $css .= "
            .grid-item {
                transition: all {$this->animations['duration']}ms ease;
            }
            .grid-item:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            }";
        }

        // Masonry CSS
        if ($this->displayModes['masonry']) {
            $css .= "
            .masonry-container .row {
                display: flex;
                flex-direction: column;
                flex-wrap: wrap;
                height: 100vh;
            }
            .masonry-container .col {
                flex: 0 0 auto;
                break-inside: avoid;
            }";
        }

        // Skeleton loading
        $css .= "
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }";

        // Thème sombre
        if ($this->theme === 'dark') {
            $css .= "
            .grid-dark .card {
                background-color: #2d3748;
                border-color: #4a5568;
            }
            .grid-dark .card-body {
                color: #e2e8f0;
            }";
        }

        $css .= '</style>';
        return $css;
    }

    /**
     * Système de plugins
     */
    public function addPlugin($name, $plugin)
    {
        if (is_callable($plugin)) {
            $this->plugins[$name] = $plugin;
        }
        return $this;
    }

    public function executePlugin($name, ...$args)
    {
        if (isset($this->plugins[$name])) {
            return call_user_func($this->plugins[$name], $this, ...$args);
        }
        return null;
    }

    /**
     * Méthodes utilitaires de conversion
     */
    // private function arrayToXml($array, $rootElement = 'grid', $xml = null)
    // {
    //     if ($xml === null) {
    //         $xml = new SimpleXMLElement('<' . $rootElement . '/>');
    //     }

    //     foreach ($array as $key => $value) {
    //         if (is_array($value)) {
    //             $this->arrayToXml($value, $key, $xml->addChild($key));
    //         } else {
    //             $xml->addChild($key, htmlspecialchars($value));
    //         }
    //     }

    //     return $xml->asXML();
    // }

    private function arrayToCsv($array)
    {
        $output = fopen('php://temp', 'w');
        
        if (!empty($array)) {
            fputcsv($output, array_keys($array[0]));
            foreach ($array as $row) {
                fputcsv($output, $row);
            }
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }

    /**
     * Méthodes de débogage
     */
    public function debug()
    {
        return [
            'elements_count' => count($this->elements),
            'config' => [
                'elements_per_row' => $this->elementsPerRow,
                'gutter' => ['x' => $this->gutterX, 'y' => $this->gutterY],
                'container_class' => $this->containerClass,
                'row_class' => $this->rowClass,
                'theme' => $this->theme
            ],
            'responsive' => $this->responsive,
            'display_modes' => $this->displayModes,
            'metrics' => $this->getMetrics(),
            'validation_errors' => $this->validate()
        ];
    }

    /**
     * Méthodes magiques pour un usage plus fluide
     */
    public function __call($method, $args)
    {
        // Permet d'appeler des plugins comme des méthodes
        if (isset($this->plugins[$method])) {
            return $this->executePlugin($method, ...$args);
        }
        
        throw new \BadMethodCallException("Méthode $method non trouvée");
    }

    public function __toString()
    {
        return $this->render(true);
    }

    public function __clone()
    {
        // Permet de cloner une grille en préservant la configuration
        $this->elements = array_map(function($element) {
            return array_merge($element, ['id' => 'grid-item-' . uniqid()]);
        }, $this->elements);
    }

    /**
     * Interface fluide pour les configurations complexes
     */
    public function configure($callback)
    {
        if (is_callable($callback)) {
            $callback($this);
        }
        return $this;
    }

    /**
     * Sauvegarde et restauration d'état
     */
    public function saveState($name = 'default')
    {
        $this->savedStates[$name] = [
            'elements' => $this->elements,
            'elementsPerRow' => $this->elementsPerRow,
            'gutterX' => $this->gutterX,
            'gutterY' => $this->gutterY,
            'responsive' => $this->responsive,
            'alignment' => $this->alignment,
            'theme' => $this->theme
        ];
        return $this;
    }

    public function restoreState($name = 'default')
    {
        if (isset($this->savedStates[$name])) {
            $state = $this->savedStates[$name];
            foreach ($state as $property => $value) {
                $this->$property = $value;
            }
        }
        return $this;
    }

    /**
     * Compte le nombre d'éléments
     */
    public function count()
    {
        return count($this->elements);
    }
}
?>