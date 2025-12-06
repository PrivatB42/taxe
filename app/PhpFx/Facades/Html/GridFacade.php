<?php

namespace PhpFx\Html;

use PhpFx\Html\Grid;

/**
 * Façade statique pour la classe Grid avec utilitaires avancés
 * Permet un usage rapide et fluide avec des méthodes statiques
 */
class GridFacade
{
    /**
     * Instance par défaut de Grid
     */
    private static $defaultInstance = null;
    
    /**
     * Instances nommées pour gérer plusieurs grilles
     */
    private static $instances = [];
    
    /**
     * Plugins globaux
     */
    private static $globalPlugins = [];
    
    /**
     * Configuration par défaut
     */
    private static $defaultConfig = [
        'elementsPerRow' => 12,
        'gutter' => ['x' => 3, 'y' => 3],
        'container' => 'fluid',
        'responsive' => true,
        'animations' => false
    ];
    
    /**
     * Templates globaux
     */
    private static $globalTemplates = [];
    
    /**
     * Thèmes prédéfinis
     */
    private static $themes = [];
    
    /**
     * Compteurs et métriques globales
     */
    private static $globalMetrics = [
        'totalGrids' => 0,
        'totalElements' => 0,
        'renderTime' => 0
    ];

    /**
     * Initialise la façade avec une configuration par défaut
     */
    public static function init($config = [])
    {
        self::$defaultConfig = array_merge(self::$defaultConfig, $config);
        self::loadDefaultPlugins();
        self::loadDefaultTemplates();
        self::loadDefaultThemes();
        return new self();
    }

    /**
     * Obtient ou crée l'instance par défaut
     */
    private static function getDefaultInstance()
    {
        if (self::$defaultInstance === null) {
            self::$defaultInstance = new Grid(self::$defaultConfig);
            self::$globalMetrics['totalGrids']++;
        }
        return self::$defaultInstance;
    }

    /**
     * Gestion d'instances nommées
     */
    public static function instance($name = 'default')
    {
        if ($name === 'default') {
            return self::getDefaultInstance();
        }
        
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new Grid(self::$defaultConfig);
            self::$globalMetrics['totalGrids']++;
        }
        
        return self::$instances[$name];
    }

    /**
     * Crée une nouvelle grille avec configuration
     */
    public static function create($config = [])
    {
        $instance = new Grid(array_merge(self::$defaultConfig, $config));
        self::$globalMetrics['totalGrids']++;
        return $instance;
    }

    /**
     * Crée rapidement une grille avec preset
     */
    public static function quick($preset = 'default', $elements = [])
    {
        $grid = self::create();
        
        if ($preset !== 'default') {
            $grid->preset($preset);
        }
        
        foreach ($elements as $element) {
            if (is_string($element)) {
                $grid->addElement($element);
            } else {
                $grid->addElement($element['content'] ?? '', $element);
            }
        }
        
        return $grid;
    }

    // ===========================================
    // MÉTHODES UTILITAIRES STATIQUES RAPIDES
    // ===========================================

    /**
     * Génère rapidement une grille de cartes
     */
    public static function cards($data, $options = [])
    {
        $grid = self::create([
            'elementsPerRow' => $options['columns'] ?? 3,
            'gutter' => $options['gutter'] ?? 4
        ]);
        
        $template = $options['template'] ?? 'card';
        $grid->addElementsFromData($data, self::getTemplate($template), $options);
        
        return $grid;
    }

    /**
     * Génère rapidement une galerie d'images
     */
    public static function gallery($images, $options = [])
    {
        $grid = self::create()->preset('portfolio', $options);
        
        foreach ($images as $image) {
            $content = is_array($image) ? 
                '<img src="' . $image['src'] . '" alt="' . ($image['alt'] ?? '') . '" class="img-fluid">' :
                '<img src="' . $image . '" alt="" class="img-fluid">';
                
            $grid->addElement($content, [
                'class' => 'gallery-item',
                'data' => is_array($image) ? $image : ['src' => $image]
            ]);
        }
        
        return $grid;
    }

    /**
     * Génère rapidement une liste de produits e-commerce
     */
    public static function products($products, $options = [])
    {
        $grid = self::create()->preset('ecommerce', $options);
        
        $template = '
        <div class="card h-100 product-card">
            <img src="{{image}}" class="card-img-top" alt="{{name}}">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">{{name}}</h5>
                <p class="card-text flex-grow-1">{{description}}</p>
                <div class="mt-auto">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 mb-0 text-primary">{{price}}</span>
                        <button class="btn btn-outline-primary">Ajouter</button>
                    </div>
                </div>
            </div>
        </div>';
        
        $grid->addElementsFromData($products, $template);
        return $grid;
    }

    /**
     * Génère rapidement un dashboard de widgets
     */
    public static function dashboard($widgets, $options = [])
    {
        $grid = self::create()->preset('dashboard', $options);
        
        foreach ($widgets as $widget) {
            $content = is_callable($widget['render']) ? 
                $widget['render']($widget) : 
                $widget['content'];
                
            $grid->addElement($content, [
                'colSize' => $widget['size'] ?? null,
                'class' => 'dashboard-widget ' . ($widget['class'] ?? ''),
                'id' => 'widget-' . ($widget['id'] ?? uniqid())
            ]);
        }
        
        return $grid;
    }

    /**
     * Génère rapidement une timeline
     */
    public static function timeline($events, $options = [])
    {
        $grid = self::create(['elementsPerRow' => 1]);
        
        foreach ($events as $index => $event) {
            $content = '
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <h5>' . $event['title'] . '</h5>
                    <p class="text-muted">' . $event['date'] . '</p>
                    <p>' . $event['description'] . '</p>
                </div>
            </div>';
            
            $grid->addElement($content, [
                'class' => 'timeline-entry',
                'data' => $event
            ]);
        }
        
        return $grid;
    }

    // ===========================================
    // MÉTHODES DE PROXY VERS L'INSTANCE PAR DÉFAUT
    // ===========================================

    public static function setElementsPerRow($count)
    {
        return self::getDefaultInstance()->setElementsPerRow($count);
    }

    public static function setGutter($x, $y = null)
    {
        return self::getDefaultInstance()->setGutter($x, $y);
    }

    public static function setContainer($type = 'fluid', $attributes = [], $classes = [])
    {
        return self::getDefaultInstance()->setContainer($type, $attributes, $classes);
    }

    public static function setResponsive($breakpoint, $elementsPerRow, $options = [])
    {
        return self::getDefaultInstance()->setResponsive($breakpoint, $elementsPerRow, $options);
    }

    public static function addElement($content, $options = [])
    {
        self::$globalMetrics['totalElements']++;
        return self::getDefaultInstance()->addElement($content, $options);
    }

    public static function addElements($elements)
    {
        $instance = self::getDefaultInstance();
        foreach ($elements as $element) {
            if (is_string($element)) {
                $instance->addElement($element);
            } else {
                $instance->addElement($element['content'] ?? '', $element);
            }
            self::$globalMetrics['totalElements']++;
        }
        return $instance;
    }

    public static function preset($preset, $options = [])
    {
        return self::getDefaultInstance()->preset($preset, $options);
    }

    public static function render($return = false)
    {
        $startTime = microtime(true);
        $result = self::getDefaultInstance()->render($return);
        self::$globalMetrics['renderTime'] += microtime(true) - $startTime;
        return $result;
    }

    // ===========================================
    // SYSTÈME DE PLUGINS AVANCÉ
    // ===========================================

    /**
     * Charge les plugins par défaut
     */
    private static function loadDefaultPlugins()
    {
        // Plugin de validation HTML
        self::addGlobalPlugin('validateHtml', function($grid, $content) {
            return filter_var($content, FILTER_SANITIZE_STRING);
        });

        // Plugin de lazy loading
        self::addGlobalPlugin('lazyLoad', function($grid, $threshold = '200px') {
            return '<div class="lazy-container" style="min-height: 200px;" data-threshold="' . $threshold . '"></div>';
        });

        // Plugin de comptage de mots
        self::addGlobalPlugin('wordCount', function($grid) {
            $totalWords = 0;
            foreach ($grid->elements as $element) {
                $totalWords += str_word_count(strip_tags($element['content']));
            }
            return $totalWords;
        });

        // Plugin de génération de sitemap
        self::addGlobalPlugin('generateSitemap', function($grid, $baseUrl = '') {
            $sitemap = ['urls' => []];
            foreach ($grid->elements as $element) {
                if (isset($element['data']['url'])) {
                    $sitemap['urls'][] = $baseUrl . $element['data']['url'];
                }
            }
            return $sitemap;
        });

        // Plugin de compression d'images
        self::addGlobalPlugin('optimizeImages', function($grid, $quality = 85) {
            // Logique de compression d'images
            return "Images optimisées avec qualité $quality%";
        });
    }

    /**
     * Ajoute un plugin global
     */
    public static function addGlobalPlugin($name, $callback)
    {
        self::$globalPlugins[$name] = $callback;
        return new self();
    }

    /**
     * Exécute un plugin global
     */
    public static function executePlugin($name, ...$args)
    {
        if (isset(self::$globalPlugins[$name])) {
            return call_user_func(self::$globalPlugins[$name], self::getDefaultInstance(), ...$args);
        }
        return null;
    }

    /**
     * Liste tous les plugins disponibles
     */
    public static function getAvailablePlugins()
    {
        return array_keys(self::$globalPlugins);
    }

    // ===========================================
    // SYSTÈME DE TEMPLATES GLOBAL
    // ===========================================

    /**
     * Charge les templates par défaut
     */
    private static function loadDefaultTemplates()
    {
        self::$globalTemplates = [
            'card' => '
            <div class="card h-100">
                <img src="{{image}}" class="card-img-top" alt="{{title}}">
                <div class="card-body">
                    <h5 class="card-title">{{title}}</h5>
                    <p class="card-text">{{description}}</p>
                    <a href="{{link}}" class="btn btn-primary">{{buttonText}}</a>
                </div>
            </div>',
            
            'testimonial' => '
            <div class="testimonial text-center p-4">
                <blockquote class="blockquote">
                    <p>"{{quote}}"</p>
                </blockquote>
                <footer class="blockquote-footer">
                    <cite title="Source Title">{{author}}</cite>
                    <small class="text-muted">{{company}}</small>
                </footer>
            </div>',
            
            'pricing' => '
            <div class="card pricing-card">
                <div class="card-header text-center">
                    <h4>{{plan}}</h4>
                    <h2 class="text-primary">{{price}}</h2>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        {{#features}}
                        <li class="mb-2">✓ {{.}}</li>
                        {{/features}}
                    </ul>
                    <button class="btn btn-primary w-100">{{buttonText}}</button>
                </div>
            </div>',
            
            'team-member' => '
            <div class="team-member text-center">
                <img src="{{photo}}" class="rounded-circle mb-3" alt="{{name}}" width="150">
                <h5>{{name}}</h5>
                <p class="text-muted">{{position}}</p>
                <p>{{bio}}</p>
                <div class="social-links">
                    <a href="{{linkedin}}" class="text-decoration-none">LinkedIn</a>
                    <a href="{{twitter}}" class="text-decoration-none">Twitter</a>
                </div>
            </div>',
            
            'feature' => '
            <div class="feature-box text-center p-4">
                <div class="feature-icon mb-3">
                    <i class="{{icon}} fa-3x text-primary"></i>
                </div>
                <h4>{{title}}</h4>
                <p>{{description}}</p>
            </div>',
            
            'blog-post' => '
            <article class="blog-post">
                <img src="{{featured_image}}" class="img-fluid mb-3" alt="{{title}}">
                <div class="post-meta text-muted mb-2">
                    <small>{{date}} par {{author}}</small>
                </div>
                <h3><a href="{{url}}" class="text-decoration-none">{{title}}</a></h3>
                <p>{{excerpt}}</p>
                <a href="{{url}}" class="btn btn-outline-primary btn-sm">Lire plus</a>
            </article>'
        ];
    }

    /**
     * Ajoute un template global
     */
    public static function addGlobalTemplate($name, $template)
    {
        self::$globalTemplates[$name] = $template;
        return new self();
    }

    /**
     * Récupère un template global
     */
    public static function getTemplate($name)
    {
        return self::$globalTemplates[$name] ?? '';
    }

    /**
     * Liste tous les templates disponibles
     */
    public static function getAvailableTemplates()
    {
        return array_keys(self::$globalTemplates);
    }

    // ===========================================
    // SYSTÈME DE THÈMES AVANCÉ
    // ===========================================

    /**
     * Charge les thèmes par défaut
     */
    private static function loadDefaultThemes()
    {
        self::$themes = [
            'bootstrap' => [
                'framework' => 'bootstrap',
                'version' => '5.3',
                'cdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'
            ],
            'material' => [
                'framework' => 'material',
                'colors' => ['primary' => '#2196F3', 'secondary' => '#FF9800'],
                'shadows' => true
            ],
            'minimal' => [
                'colors' => ['primary' => '#000', 'secondary' => '#666'],
                'spacing' => 'tight',
                'typography' => 'clean'
            ]
        ];
    }

    /**
     * Applique un thème global
     */
    public static function applyTheme($themeName, $instance = null)
    {
        $target = $instance ?? self::getDefaultInstance();
        
        if (isset(self::$themes[$themeName])) {
            $target->loadTheme($themeName);
        }
        
        return $target;
    }

    // ===========================================
    // UTILITAIRES DE GÉNÉRATION RAPIDE
    // ===========================================

    /**
     * Génère le HTML complet avec CDN et scripts
     */
    public static function renderComplete($title = 'Grid Layout', $theme = 'bootstrap')
    {
        $grid = self::getDefaultInstance();
        $content = $grid->render(true);
        
        $html = '<!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . htmlspecialchars($title) . '</title>';
            
        if ($theme === 'bootstrap') {
            $html .= '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
        }
        
        $html .= $grid->generateCustomCSS();
        $html .= '</head>
        <body>
            <main class="py-4">
                ' . $content . '
            </main>';
            
        if ($theme === 'bootstrap') {
            $html .= '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>';
        }
        
        $html .= '</body>
        </html>';
        
        return $html;
    }

    /**
     * Sauvegarde la grille dans un fichier
     */
    public static function saveToFile($filename, $format = 'html')
    {
        $content = '';
        
        switch ($format) {
            case 'html':
                $content = self::renderComplete();
                break;
            case 'json':
                $content = self::getDefaultInstance()->export('json');
                break;
            case 'xml':
                $content = self::getDefaultInstance()->export('xml');
                break;
        }
        
        return file_put_contents($filename, $content);
    }

    /**
     * Génère un PDF de la grille (nécessite une librairie PDF)
     */
    public static function generatePDF($filename, $options = [])
    {
        // Cette méthode nécessiterait une librairie comme TCPDF ou mPDF
        // Exemple conceptuel :
        /*
        $html = self::renderComplete();
        $pdf = new \TCPDF();
        $pdf->AddPage();
        $pdf->writeHTML($html);
        $pdf->Output($filename, 'F');
        */
        
        return "PDF généré : $filename (nécessite une librairie PDF)";
    }

    // ===========================================
    // ANALYTICS ET MÉTRIQUES
    // ===========================================

    /**
     * Retourne les métriques globales
     */
    public static function getGlobalMetrics()
    {
        return array_merge(self::$globalMetrics, [
            'instancesCreated' => count(self::$instances) + (self::$defaultInstance ? 1 : 0),
            'averageElementsPerGrid' => self::$globalMetrics['totalGrids'] > 0 ? 
                round(self::$globalMetrics['totalElements'] / self::$globalMetrics['totalGrids'], 2) : 0,
            'averageRenderTime' => self::$globalMetrics['renderTime'],
            'availablePlugins' => count(self::$globalPlugins),
            'availableTemplates' => count(self::$globalTemplates),
            'memoryUsage' => memory_get_usage(true),
            'peakMemoryUsage' => memory_get_peak_usage(true)
        ]);
    }

    /**
     * Génère un rapport détaillé
     */
    public static function generateReport()
    {
        $metrics = self::getGlobalMetrics();
        
        $report = [
            'summary' => $metrics,
            'instances' => [],
            'plugins' => self::getAvailablePlugins(),
            'templates' => self::getAvailableTemplates(),
            'performance' => [
                'total_render_time' => $metrics['averageRenderTime'],
                'memory_efficiency' => round($metrics['memoryUsage'] / 1024 / 1024, 2) . ' MB'
            ]
        ];
        
        // Détails des instances
        if (self::$defaultInstance) {
            $report['instances']['default'] = self::$defaultInstance->getMetrics();
        }
        
        foreach (self::$instances as $name => $instance) {
            $report['instances'][$name] = $instance->getMetrics();
        }
        
        return $report;
    }

    // ===========================================
    // MÉTHODES DE BATCH ET TRAITEMENT EN LOT
    // ===========================================

    /**
     * Traite plusieurs grilles en lot
     */
    public static function batch($operations)
    {
        $results = [];
        
        foreach ($operations as $name => $operation) {
            $grid = self::instance($name);
            
            if (isset($operation['preset'])) {
                $grid->preset($operation['preset']);
            }
            
            if (isset($operation['elements'])) {
                foreach ($operation['elements'] as $element) {
                    $grid->addElement($element['content'] ?? '', $element);
                }
            }
            
            if (isset($operation['plugins'])) {
                foreach ($operation['plugins'] as $plugin => $args) {
                    $results[$name]['plugins'][$plugin] = $grid->executePlugin($plugin, ...$args);
                }
            }
            
            $results[$name]['html'] = $grid->render(true);
            $results[$name]['metrics'] = $grid->getMetrics();
        }
        
        return $results;
    }

    // ===========================================
    // MÉTHODES MAGIQUES ET UTILITAIRES
    // ===========================================

    /**
     * Appel de méthodes statiques dynamiques
     */
    public static function __callStatic($method, $args)
    {
        // Vérifie si c'est un plugin
        if (isset(self::$globalPlugins[$method])) {
            return self::executePlugin($method, ...$args);
        }
        
        // Délègue à l'instance par défaut
        $instance = self::getDefaultInstance();
        if (method_exists($instance, $method)) {
            return call_user_func_array([$instance, $method], $args);
        }
        
        throw new \BadMethodCallException("Méthode statique $method non trouvée");
    }

    /**
     * Reset de l'état global
     */
    public static function reset()
    {
        self::$defaultInstance = null;
        self::$instances = [];
        self::$globalMetrics = [
            'totalGrids' => 0,
            'totalElements' => 0,
            'renderTime' => 0
        ];
        
        return new self();
    }

    /**
     * Configuration globale
     */
    public static function configure($config)
    {
        self::$defaultConfig = array_merge(self::$defaultConfig, $config);
        return new self();
    }

    /**
     * Version et informations
     */
    public static function version()
    {
        return [
            'version' => '2.0.0',
            'php_version' => PHP_VERSION,
            'features' => [
                'static_facade' => true,
                'multiple_instances' => true,
                'global_plugins' => true,
                'batch_processing' => true,
                'analytics' => true
            ]
        ];
    }
}
?>