/**
 * Bibliothèque X_SELECT - Wrapper avancé pour Tom Select
 * @version 1.0
 * @license MIT
 * @requires Tom Select
 */
const X_SELECT = {
    // ======================== CONFIGURATION GLOBALE ========================
    
    /** Configuration par défaut */
    defaultConfig: {
        create: false,
        createOnBlur: true,
        persist: false,
        maxItems: null,
        searchField: ['text', 'value'],
        valueField: 'value',
        labelField: 'text',
        sortField: 'text',
        placeholder: 'Sélectionnez...',
        loadThrottle: 300,
        preload: false,
        allowEmptyOption: false,
        closeAfterSelect: false,
        selectOnTab: false,
        copyClassesToDropdown: true
    },

    /** Instances actives */
    instances: new Map(),

    // ======================== MÉTHODES DE CRÉATION PRINCIPALES ========================

    /** Créer un select simple avec données statiques */
    create(selector, options = [], config = {}) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (!element) throw new Error(`Élément non trouvé: ${selector}`);

        const finalConfig = { ...this.defaultConfig, ...config };
        
        // Préparer les options
        if (Array.isArray(options)) {
            finalConfig.options = options.map(opt => 
                typeof opt === 'string' ? { value: opt, text: opt } : opt
            );
        }

        const instance = new TomSelect(element, finalConfig);
        this.instances.set(element, instance);
        
        return this._createWrapper(instance, element);
    },

    /** Créer un select avec données AJAX */
    ajax(selector, config = {}) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (!element) throw new Error(`Élément non trouvé: ${selector}`);

        const ajaxConfig = {
            ...this.defaultConfig,
            ...config,
            load: (query, callback) => {
                if (!query.length && !config.preload) return callback();
                
                const url = typeof config.url === 'function' 
                    ? config.url(query) 
                    : `${config.url}${config.url.includes('?') ? '&' : '?'}q=${encodeURIComponent(query)}`;

                fetch(url, config.fetchOptions || {})
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        const items = config.transform ? config.transform(data) : data;
                        callback(items);
                    })
                    .catch(() => callback());
            }
        };

        const instance = new TomSelect(element, ajaxConfig);
        this.instances.set(element, instance);
        
        return this._createWrapper(instance, element);
    },

    /** Créer un select avec fonction de rendu personnalisée */
    render(selector, renderConfig = {}, config = {}) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (!element) throw new Error(`Élément non trouvé: ${selector}`);

        const finalConfig = {
            ...this.defaultConfig,
            ...config,
            render: {
                option: renderConfig.option || null,
                item: renderConfig.item || null,
                option_create: renderConfig.optionCreate || null,
                no_results: renderConfig.noResults || null,
                loading: renderConfig.loading || null,
                ...renderConfig
            }
        };

        const instance = new TomSelect(element, finalConfig);
        this.instances.set(element, instance);
        
        return this._createWrapper(instance, element);
    },

    /** Créer un select multiple */
    multiple(selector, options = [], config = {}) {
        return this.create(selector, options, {
            ...config,
            maxItems: null,
            plugins: ['remove_button', ...(config.plugins || [])]
        });
    },

    /** Créer un select avec tags (création dynamique) */
    tags(selector, config = {}) {
        return this.create(selector, [], {
            ...config,
            create: true,
            persist: false,
            maxItems: null,
            plugins: ['remove_button', ...(config.plugins || [])]
        });
    },

    // ======================== WRAPPER PRINCIPAL ========================

    _createWrapper(instance, element) {
        return {
            // Instance Tom Select native
            instance,
            element,

            // ======================== GESTION DES DONNÉES ========================

            /** Définir les options */
            setOptions(options) {
                instance.clearOptions();
                instance.addOptions(options);
                return this;
            },

            /** Ajouter des options */
            addOptions(options) {
                instance.addOptions(options);
                return this;
            },

            /** Ajouter une option */
            addOption(option) {
                instance.addOption(option);
                return this;
            },

            /** Supprimer une option */
            removeOption(value) {
                instance.removeOption(value);
                return this;
            },

            /** Vider toutes les options */
            clearOptions() {
                instance.clearOptions();
                return this;
            },

            /** Obtenir toutes les options */
            getOptions() {
                return instance.options;
            },

            /** Obtenir une option spécifique */
            getOption(value) {
                return instance.options[value];
            },

            // ======================== GESTION DE LA SÉLECTION ========================

            /** Définir la valeur */
            setValue(value, silent = false) {
                instance.setValue(value, silent);
                return this;
            },

            /** Obtenir la valeur */
            getValue() {
                return instance.getValue();
            },

            /** Ajouter un item à la sélection */
            addItem(value, silent = false) {
                instance.addItem(value, silent);
                return this;
            },

            /** Supprimer un item de la sélection */
            removeItem(value, silent = false) {
                instance.removeItem(value, silent);
                return this;
            },

            /** Vider la sélection */
            clear(silent = false) {
                instance.clear(silent);
                return this;
            },

            /** Sélectionner tout (pour les selects multiples) */
            selectAll() {
                Object.keys(instance.options).forEach(value => {
                    instance.addItem(value, true);
                });
                instance.refreshItems();
                return this;
            },

            /** Inverser la sélection */
            invertSelection() {
                const selected = Array.isArray(instance.getValue()) 
                    ? instance.getValue() 
                    : [instance.getValue()].filter(Boolean);
                
                instance.clear(true);
                Object.keys(instance.options).forEach(value => {
                    if (!selected.includes(value)) {
                        instance.addItem(value, true);
                    }
                });
                instance.refreshItems();
                return this;
            },

            // ======================== GESTION DE L'ÉTAT ========================

            /** Activer le select */
            enable() {
                instance.enable();
                return this;
            },

            /** Désactiver le select */
            disable() {
                instance.disable();
                return this;
            },

            /** Verrouiller le select */
            lock() {
                instance.lock();
                return this;
            },

            /** Déverrouiller le select */
            unlock() {
                instance.unlock();
                return this;
            },

            /** Vérifier si désactivé */
            isDisabled() {
                return instance.isDisabled;
            },

            /** Vérifier si verrouillé */
            isLocked() {
                return instance.isLocked;
            },

            /** Vérifier si ouvert */
            isOpen() {
                return instance.isOpen;
            },

            // ======================== GESTION DE L'INTERFACE ========================

            /** Ouvrir le dropdown */
            open() {
                instance.open();
                return this;
            },

            /** Fermer le dropdown */
            close() {
                instance.close();
                return this;
            },

            /** Basculer l'ouverture */
            toggle() {
                if (instance.isOpen) {
                    instance.close();
                } else {
                    instance.open();
                }
                return this;
            },

            /** Focus sur l'élément */
            focus() {
                instance.focus();
                return this;
            },

            /** Blur l'élément */
            blur() {
                instance.blur();
                return this;
            },

            // ======================== GESTION DE LA RECHERCHE ========================

            /** Effectuer une recherche */
            search(query) {
                instance.setTextboxValue(query);
                instance.onSearchChange(query);
                return this;
            },

            /** Vider la recherche */
            clearSearch() {
                instance.setTextboxValue('');
                return this;
            },

            /** Obtenir le terme de recherche actuel */
            getSearchValue() {
                return instance.lastValue;
            },

            // ======================== MISE À JOUR ET SYNCHRONISATION ========================

            /** Rafraîchir les items */
            refresh() {
                instance.refreshItems();
                instance.refreshOptions();
                return this;
            },

            /** Synchroniser avec l'élément original */
            sync() {
                instance.sync();
                return this;
            },

            /** Recharger les données AJAX */
            reload() {
                if (instance.settings.load) {
                    instance.clearOptions();
                    instance.load('');
                }
                return this;
            },

            // ======================== GESTION DES ÉVÉNEMENTS ========================

            /** Ajouter un écouteur d'événement */
            on(event, callback) {
                instance.on(event, callback);
                return this;
            },

            /** Supprimer un écouteur d'événement */
            off(event, callback) {
                instance.off(event, callback);
                return this;
            },

            /** Émettre un événement */
            trigger(event, ...args) {
                instance.trigger(event, ...args);
                return this;
            },

            /** Gestionnaire d'événements en lot */
            onMultiple(events, callback) {
                const eventList = Array.isArray(events) ? events : events.split(' ');
                eventList.forEach(event => instance.on(event.trim(), callback));
                return this;
            },

            /** Événement une seule fois */
            once(event, callback) {
                const wrapper = (...args) => {
                    callback(...args);
                    instance.off(event, wrapper);
                };
                instance.on(event, wrapper);
                return this;
            },

            /** Débounce d'événement */
            onDebounced(event, callback, delay = 300) {
                let timeoutId;
                const debouncedCallback = (...args) => {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => callback(...args), delay);
                };
                instance.on(event, debouncedCallback);
                return this;
            },

            /** Throttle d'événement */
            onThrottled(event, callback, limit = 100) {
                let inThrottle;
                const throttledCallback = (...args) => {
                    if (!inThrottle) {
                        callback(...args);
                        inThrottle = true;
                        setTimeout(() => inThrottle = false, limit);
                    }
                };
                instance.on(event, throttledCallback);
                return this;
            },

            /** Supprimer tous les événements */
            offAll() {
                Object.keys(instance.eventNS).forEach(event => {
                    instance.off(event);
                });
                return this;
            },

            /** Émettre un événement personnalisé */
            emit(eventName, data = {}) {
                const event = new CustomEvent(`tomselect:${eventName}`, { 
                    detail: { instance, element, data } 
                });
                element.dispatchEvent(event);
                return this;
            },

            // ======================== GESTION DES PLUGINS ========================

            /** Ajouter un plugin */
            addPlugin(name, options = {}) {
                instance.addPlugin(name, options);
                return this;
            },

            /** Supprimer un plugin */
            removePlugin(name) {
                instance.removePlugin(name);
                return this;
            },

            // ======================== MÉTHODES UTILITAIRES ========================

            /** Obtenir le nombre d'options */
            getOptionCount() {
                return Object.keys(instance.options).length;
            },

            /** Obtenir le nombre d'items sélectionnés */
            getSelectedCount() {
                const value = instance.getValue();
                return Array.isArray(value) ? value.length : (value ? 1 : 0);
            },

            /** Vérifier si une valeur est sélectionnée */
            isSelected(value) {
                const selected = instance.getValue();
                return Array.isArray(selected) 
                    ? selected.includes(value)
                    : selected === value;
            },

            /** Obtenir les valeurs non sélectionnées */
            getUnselected() {
                const selected = Array.isArray(instance.getValue()) 
                    ? instance.getValue() 
                    : [instance.getValue()].filter(Boolean);
                
                return Object.keys(instance.options).filter(value => 
                    !selected.includes(value)
                );
            },

            /** Filtrer les options */
            filterOptions(predicate) {
                const filtered = {};
                Object.entries(instance.options).forEach(([key, option]) => {
                    if (predicate(option, key)) {
                        filtered[key] = option;
                    }
                });
                return filtered;
            },

            // ======================== IMPORT/EXPORT ========================

            /** Exporter les données */
            export(format = 'json') {
                const data = {
                    options: instance.options,
                    value: instance.getValue(),
                    settings: instance.settings
                };

                switch (format) {
                    case 'json':
                        return JSON.stringify(data, null, 2);
                    case 'csv':
                        return this._exportToCsv(data);
                    default:
                        return data;
                }
            },

            /** Importer des données */
            import(data, format = 'json') {
                let parsedData = data;
                
                if (format === 'json' && typeof data === 'string') {
                    parsedData = JSON.parse(data);
                }

                if (parsedData.options) {
                    this.setOptions(parsedData.options);
                }
                if (parsedData.value !== undefined) {
                    this.setValue(parsedData.value);
                }

                return this;
            },

            _exportToCsv(data) {
                const rows = [['value', 'text']];
                Object.entries(data.options).forEach(([key, option]) => {
                    rows.push([key, option.text || option.label || key]);
                });
                return rows.map(row => row.join(',')).join('\n');
            },

            // ======================== VALIDATION ========================

            /** Valider la sélection */
            validate(rules = {}) {
                const value = instance.getValue();
                const errors = [];

                if (rules.required && (!value || (Array.isArray(value) && value.length === 0))) {
                    errors.push('Sélection requise');
                }

                if (rules.min && Array.isArray(value) && value.length < rules.min) {
                    errors.push(`Minimum ${rules.min} sélection(s) requise(s)`);
                }

                if (rules.max && Array.isArray(value) && value.length > rules.max) {
                    errors.push(`Maximum ${rules.max} sélection(s) autorisée(s)`);
                }

                if (rules.custom && typeof rules.custom === 'function') {
                    const customError = rules.custom(value, instance.options);
                    if (customError) errors.push(customError);
                }

                return {
                    valid: errors.length === 0,
                    errors
                };
            },

            /** Validation HTML5 personnalisée */
            setCustomValidity(message) {
                element.setCustomValidity(message);
                return this;
            },

            // ======================== CONFIGURATION DYNAMIQUE ========================

            /** Convertir en AJAX n'importe quel select existant */
            setAjax(ajaxConfig) {
                const newConfig = {
                    ...instance.settings,
                    load: (query, callback) => {
                        if (!query.length && !ajaxConfig.preload) return callback();
                        
                        const url = typeof ajaxConfig.url === 'function' 
                            ? ajaxConfig.url(query) 
                            : `${ajaxConfig.url}${ajaxConfig.url.includes('?') ? '&' : '?'}q=${encodeURIComponent(query)}`;

                        fetch(url, ajaxConfig.fetchOptions || {})
                            .then(response => {
                                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                                return response.json();
                            })
                            .then(data => {
                                const items = ajaxConfig.transform ? ajaxConfig.transform(data) : data;
                                callback(items);
                            })
                            .catch(error => {
                                console.error('Ajax error:', error);
                                if (ajaxConfig.onError) ajaxConfig.onError(error);
                                callback();
                            });
                    },
                    ...ajaxConfig
                };

                // Reconfigurer l'instance
                instance.destroy();
                const newInstance = new TomSelect(element, newConfig);
                X_SELECT.instances.set(element, newInstance);
                
                return X_SELECT._createWrapper(newInstance, element);
            },

            /** Définir facilement les rendus avec templates */
            setRender(optionTemplate, itemTemplate = null) {
                const renderConfig = {};
                
                if (optionTemplate) {
                    renderConfig.option = function(data, escape) {
                        return X_SELECT._processTemplate(optionTemplate, data, escape);
                    };
                }
                
                if (itemTemplate) {
                    renderConfig.item = function(data, escape) {
                        return X_SELECT._processTemplate(itemTemplate, data, escape);
                    };
                }

                instance.settings.render = { ...instance.settings.render, ...renderConfig };
                instance.refreshOptions();
                
                return this;
            },

            /** Définir des templates de rendu avancés */
            setAdvancedRender(renderConfig) {
                const templates = {};
                
                Object.entries(renderConfig).forEach(([key, template]) => {
                    if (typeof template === 'string') {
                        templates[key] = function(data, escape) {
                            return X_SELECT._processTemplate(template, data, escape);
                        };
                    } else {
                        templates[key] = template;
                    }
                });

                instance.settings.render = { ...instance.settings.render, ...templates };
                instance.refreshOptions();
                
                return this;
            },

            // ======================== ANIMATIONS ET TRANSITIONS ========================

            /** Animer l'ouverture/fermeture */
            animatedToggle(duration = 300) {
                const dropdown = instance.dropdown;
                
                if (instance.isOpen) {
                    dropdown.style.transition = `opacity ${duration}ms ease`;
                    dropdown.style.opacity = '0';
                    setTimeout(() => instance.close(), duration);
                } else {
                    instance.open();
                    dropdown.style.transition = `opacity ${duration}ms ease`;
                    dropdown.style.opacity = '0';
                    requestAnimationFrame(() => {
                        dropdown.style.opacity = '1';
                    });
                }
                
                return this;
            },

            /** Effet de shake pour erreur */
            shake(duration = 500) {
                const wrapper = instance.wrapper;
                wrapper.style.animation = `shake 0.5s`;
                setTimeout(() => {
                    wrapper.style.animation = '';
                }, duration);
                return this;
            },

            /** Effet de pulse pour attirer l'attention */
            pulse(duration = 1000) {
                const wrapper = instance.wrapper;
                wrapper.style.animation = `pulse 1s infinite`;
                setTimeout(() => {
                    wrapper.style.animation = '';
                }, duration);
                return this;
            },

            // ======================== SÉCURITÉ ET NETTOYAGE ========================

            /** Nettoyer et échapper les valeurs */
            sanitizeValue(value) {
                if (typeof value === 'string') {
                    return value.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
                }
                return value;
            },

            /** Valider et nettoyer les options avant ajout */
            addSecureOptions(options) {
                const cleanOptions = options.map(opt => ({
                    ...opt,
                    text: this.sanitizeValue(opt.text),
                    value: this.sanitizeValue(opt.value)
                }));
                instance.addOptions(cleanOptions);
                return this;
            },

            // ======================== PERFORMANCE ET OPTIMISATION ========================

            /** Mode batch pour opérations multiples */
            batch(operations) {
                instance.settings.silent = true;
                
                try {
                    operations(this);
                } finally {
                    instance.settings.silent = false;
                    instance.refreshItems();
                    instance.refreshOptions();
                }
                
                return this;
            },

            /** Lazy loading des options */
            enableLazyLoading(pageSize = 50) {
                let currentPage = 0;
                const originalOptions = [...Object.values(instance.options)];
                
                instance.clearOptions();
                
                const loadPage = () => {
                    const start = currentPage * pageSize;
                    const end = start + pageSize;
                    const pageOptions = originalOptions.slice(start, end);
                    
                    if (pageOptions.length > 0) {
                        instance.addOptions(pageOptions);
                        currentPage++;
                    }
                };

                // Charger la première page
                loadPage();

                // Charger plus au scroll
                instance.dropdown_content.addEventListener('scroll', () => {
                    const { scrollTop, scrollHeight, clientHeight } = instance.dropdown_content;
                    
                    if (scrollTop + clientHeight >= scrollHeight - 10) {
                        loadPage();
                    }
                });

                return this;
            },

            // ======================== THÈMES ET STYLES ========================

            /** Appliquer un thème */
            setTheme(theme) {
                const themes = {
                    dark: {
                        '--ts-pr-clear-button': '#666',
                        '--ts-pr-caret': '#666',
                        '--ts-pr-bg': '#2d2d2d',
                        '--ts-pr-color': '#fff',
                        '--ts-pr-border': '#444'
                    },
                    light: {
                        '--ts-pr-clear-button': '#999',
                        '--ts-pr-caret': '#999',
                        '--ts-pr-bg': '#fff',
                        '--ts-pr-color': '#000',
                        '--ts-pr-border': '#ccc'
                    },
                    primary: {
                        '--ts-pr-border': '#007bff',
                        '--ts-pr-border-focus': '#0056b3'
                    },
                    success: {
                        '--ts-pr-border': '#28a745',
                        '--ts-pr-border-focus': '#1e7e34'
                    },
                    danger: {
                        '--ts-pr-border': '#dc3545',
                        '--ts-pr-border-focus': '#bd2130'
                    }
                };

                const themeVars = themes[theme] || theme;
                Object.entries(themeVars).forEach(([property, value]) => {
                    instance.wrapper.style.setProperty(property, value);
                });

                return this;
            },

            /** Ajouter une classe CSS conditionnelle */
            toggleClass(className, condition) {
                if (condition) {
                    instance.wrapper.classList.add(className);
                } else {
                    instance.wrapper.classList.remove(className);
                }
                return this;
            },

            // ======================== ACCESSIBILITÉ ========================

            /** Améliorer l'accessibilité */
            enhanceA11y(options = {}) {
                const config = {
                    announceSelection: true,
                    announceCount: true,
                    customAnnouncements: {},
                    ...options
                };

                if (config.announceSelection) {
                    this.on('item_add', (value, item) => {
                        this._announce(`${item.textContent || value} sélectionné`);
                    });

                    this.on('item_remove', (value) => {
                        this._announce(`${value} désélectionné`);
                    });
                }

                if (config.announceCount) {
                    this.on('change', () => {
                        const count = this.getSelectedCount();
                        this._announce(`${count} élément(s) sélectionné(s)`);
                    });
                }

                return this;
            },

            /** Annoncer pour les lecteurs d'écran */
            _announce(message) {
                const announcer = document.createElement('div');
                announcer.setAttribute('aria-live', 'polite');
                announcer.setAttribute('aria-atomic', 'true');
                announcer.style.position = 'absolute';
                announcer.style.left = '-10000px';
                announcer.textContent = message;
                
                document.body.appendChild(announcer);
                setTimeout(() => document.body.removeChild(announcer), 1000);
            },

            // ======================== INTÉGRATION FORMULAIRES ========================

            /** Synchronisation avec formulaires */
            syncWithForm(formSelector) {
                const form = document.querySelector(formSelector);
                if (!form) return this;

                // Reset au reset du formulaire
                form.addEventListener('reset', () => {
                    setTimeout(() => this.clear(), 0);
                });

                // Validation HTML5
                this.on('change', () => {
                    element.setCustomValidity('');
                });

                return this;
            },

            // ======================== DESTRUCTION ========================

            /** Détruire l'instance */
            destroy() {
                X_SELECT.instances.delete(element);
                instance.destroy();
                return null;
            }
        };
    },

    // ======================== MÉTHODES UTILITAIRES GLOBALES ========================

    /** Traiter les templates avec données */
    _processTemplate(template, data, escape) {
        return template.replace(/\{\{(\w+)\}\}/g, (match, key) => {
            const value = data[key] || '';
            return escape ? escape(value) : value;
        });
    },

    /** Créer un observable pour les changements */
    createObservable() {
        const observers = [];
        
        return {
            subscribe(callback) {
                observers.push(callback);
                return () => {
                    const index = observers.indexOf(callback);
                    if (index > -1) observers.splice(index, 1);
                };
            },
            notify(data) {
                observers.forEach(callback => callback(data));
            }
        };
    },

    /** Gestionnaire de cache global */
    cache: {
        store: new Map(),
        
        get(key) {
            const item = this.store.get(key);
            if (!item) return null;
            
            if (item.expires && Date.now() > item.expires) {
                this.store.delete(key);
                return null;
            }
            
            return item.data;
        },
        
        set(key, data, ttl = 300000) { // 5 minutes par défaut
            this.store.set(key, {
                data,
                expires: ttl ? Date.now() + ttl : null
            });
        },
        
        clear() {
            this.store.clear();
        }
    },

    /** Débounce global */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /** Throttle global */
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    /** Utilitaires de validation */
    validators: {
        email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
        url: (value) => {
            try { new URL(value); return true; } 
            catch { return false; }
        },
        numeric: (value) => !isNaN(parseFloat(value)) && isFinite(value),
        minLength: (value, min) => value.toString().length >= min,
        maxLength: (value, max) => value.toString().length <= max,
        pattern: (value, regex) => regex.test(value),
        custom: (value, validator) => validator(value)
    },

    /** Middleware pour les opérations */
    middleware: {
        beforeCreate: [],
        afterCreate: [],
        beforeDestroy: [],
        afterDestroy: [],
        
        add(hook, callback) {
            if (this[hook]) {
                this[hook].push(callback);
            }
        },
        
        execute(hook, data) {
            if (this[hook]) {
                this[hook].forEach(callback => callback(data));
            }
        }
    },

    /** Système de plugins pour X_SELECT */
    plugins: {
        registered: new Map(),
        
        register(name, plugin) {
            this.registered.set(name, plugin);
        },
        
        use(instance, pluginName, options = {}) {
            const plugin = this.registered.get(pluginName);
            if (plugin) {
                plugin(instance, options);
            }
        }
    },

    // ======================== GESTION DES INSTANCES ========================

    /** Obtenir une instance */
    getInstance(selector) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        return this.instances.get(element);
    },

    /** Détruire toutes les instances */
    destroyAll() {
        this.instances.forEach(instance => instance.destroy());
        this.instances.clear();
    },

    /** Obtenir toutes les instances */
    getAllInstances() {
        return Array.from(this.instances.values());
    },

    // ======================== PRESETS PRÉDÉFINIS ========================

    /** Créer des presets personnalisés */
    createPreset(name, factory) {
        this[name] = factory.bind(this);
        
        // Créer aussi l'alias global
        if (typeof window !== 'undefined') {
            window[`x_select_${name}`] = this[name];
        }
        
        return this;
    },

    /** Preset pour sélection de pays */
    countries(selector, config = {}) {
        const countries = [
            { value: 'FR', text: '🇫🇷 France' },
            { value: 'US', text: '🇺🇸 États-Unis' },
            { value: 'DE', text: '🇩🇪 Allemagne' },
            { value: 'GB', text: '🇬🇧 Royaume-Uni' },
            { value: 'ES', text: '🇪🇸 Espagne' },
            { value: 'IT', text: '🇮🇹 Italie' },
            { value: 'CA', text: '🇨🇦 Canada' },
            { value: 'AU', text: '🇦🇺 Australie' },
            { value: 'JP', text: '🇯🇵 Japon' },
            { value: 'CN', text: '🇨🇳 Chine' }
        ];

        return this.create(selector, countries, {
            searchField: ['text'],
            placeholder: 'Sélectionnez un pays...',
            ...config
        });
    },

    /** Preset pour sélection de devises */
    currencies(selector, config = {}) {
        const currencies = [
            { value: 'USD', text: '🇺🇸 Dollar US (USD)' },
            { value: 'EUR', text: '🇪🇺 Euro (EUR)' },
            { value: 'GBP', text: '🇬🇧 Livre Sterling (GBP)' },
            { value: 'JPY', text: '🇯🇵 Yen (JPY)' },
            { value: 'CAD', text: '🇨🇦 Dollar Canadien (CAD)' },
            { value: 'AUD', text: '🇦🇺 Dollar Australien (AUD)' },
            { value: 'CHF', text: '🇨🇭 Franc Suisse (CHF)' },
            { value: 'CNY', text: '🇨🇳 Yuan Chinois (CNY)' }
        ];

        return this.create(selector, currencies, {
            searchField: ['text', 'value'],
            placeholder: 'Sélectionnez une devise...',
            ...config
        });
    },

    /** Preset pour sélection de langues */
    languages(selector, config = {}) {
        const languages = [
            { value: 'fr', text: '🇫🇷 Français' },
            { value: 'en', text: '🇺🇸 English' },
            { value: 'es', text: '🇪🇸 Español' },
            { value: 'de', text: '🇩🇪 Deutsch' },
            { value: 'it', text: '🇮🇹 Italiano' },
            { value: 'pt', text: '🇵🇹 Português' },
            { value: 'ru', text: '🇷🇺 Русский' },
            { value: 'zh', text: '🇨🇳 中文' },
            { value: 'ja', text: '🇯🇵 日本語' },
            { value: 'ar', text: '🇸🇦 العربية' }
        ];

        return this.create(selector, languages, {
            searchField: ['text', 'value'],
            placeholder: 'Sélectionnez une langue...',
            ...config
        });
    },

    /** Preset pour sélection de fuseaux horaires */
    timezones(selector, config = {}) {
        const timezones = [
            { value: 'UTC', text: 'UTC (Temps Universel)' },
            { value: 'Europe/Paris', text: 'Europe/Paris (GMT+1)' },
            { value: 'America/New_York', text: 'America/New_York (GMT-5)' },
            { value: 'America/Los_Angeles', text: 'America/Los_Angeles (GMT-8)' },
            { value: 'Asia/Tokyo', text: 'Asia/Tokyo (GMT+9)' },
            { value: 'Australia/Sydney', text: 'Australia/Sydney (GMT+10)' },
            { value: 'Europe/London', text: 'Europe/London (GMT+0)' },
            { value: 'America/Chicago', text: 'America/Chicago (GMT-6)' }
        ];

        return this.create(selector, timezones, {
            searchField: ['text'],
            placeholder: 'Sélectionnez un fuseau horaire...',
            ...config
        });
    },

    /** Preset pour évaluation par étoiles */
    rating(selector, config = {}) {
        const ratings = [
            { value: '5', text: '⭐⭐⭐⭐⭐ Excellent' },
            { value: '4', text: '⭐⭐⭐⭐ Très bon' },
            { value: '3', text: '⭐⭐⭐ Bon' },
            { value: '2', text: '⭐⭐ Moyen' },
            { value: '1', text: '⭐ Mauvais' }
        ];

        return this.create(selector, ratings, {
            placeholder: 'Évaluez...',
            ...config
        });
    },

    /** Preset pour priorités */
    priority(selector, config = {}) {
        const priorities = [
            { value: 'urgent', text: '🔴 Urgent', color: '#dc3545' },
            { value: 'high', text: '🟠 Haute', color: '#fd7e14' },
            { value: 'medium', text: '🟡 Moyenne', color: '#ffc107' },
            { value: 'low', text: '🟢 Basse', color: '#28a745' },
            { value: 'none', text: '⚪ Aucune', color: '#6c757d' }
        ];

        return this.render(selector, {
            option: (data) => `<div><span style="color:${data.color}">${data.text}</span></div>`,
            item: (data) => `<span style="color:${data.color}">${data.text}</span>`
        }, {
            options: priorities,
            placeholder: 'Sélectionnez une priorité...',
            ...config
        });
    },

    /** Preset pour statuts */
    status(selector, config = {}) {
        const statuses = [
            { value: 'active', text: '✅ Actif', color: '#28a745' },
            { value: 'inactive', text: '⏸️ Inactif', color: '#6c757d' },
            { value: 'pending', text: '⏳ En attente', color: '#ffc107' },
            { value: 'blocked', text: '🚫 Bloqué', color: '#dc3545' }
        ];

        return this.render(selector, {
            option: (data) => `<div><span style="color:${data.color}">${data.text}</span></div>`,
            item: (data) => `<span style="color:${data.color}">${data.text}</span>`
        }, {
            options: statuses,
            placeholder: 'Sélectionnez un statut...',
            ...config
        });
    }
};

// ======================== PLUGINS INTÉGRÉS ========================

// Plugin de synchronisation multi-selects
X_SELECT.plugins.register('sync', function(instance, options = {}) {
    const { targets = [], mode = 'mirror' } = options;
    
    instance.on('change', (value) => {
        targets.forEach(targetSelector => {
            const targetInstance = X_SELECT.getInstance(targetSelector);
            if (targetInstance) {
                if (mode === 'mirror') {
                    targetInstance.setValue(value, true);
                } else if (mode === 'exclude') {
                    const currentValues = Array.isArray(targetInstance.getValue()) 
                        ? targetInstance.getValue() 
                        : [targetInstance.getValue()].filter(Boolean);
                    
                    const selectedValues = Array.isArray(value) ? value : [value].filter(Boolean);
                    const filteredValues = currentValues.filter(v => !selectedValues.includes(v));
                    
                    targetInstance.setValue(filteredValues, true);
                }
            }
        });
    });
});

// Plugin de sauvegarde automatique
X_SELECT.plugins.register('autosave', function(instance, options = {}) {
    const { key, storage = 'localStorage', debounceTime = 1000 } = options;
    const storageKey = `x_select_${key || instance.element.id}`;
    
    // Charger la valeur sauvegardée
    const savedValue = window[storage].getItem(storageKey);
    if (savedValue) {
        try {
            instance.setValue(JSON.parse(savedValue), true);
        } catch (e) {
            console.warn('Impossible de charger la valeur sauvegardée:', e);
        }
    }
    
    // Sauvegarder les changements
    const saveValue = X_SELECT.debounce((value) => {
        window[storage].setItem(storageKey, JSON.stringify(value));
    }, debounceTime);
    
    instance.on('change', saveValue);
});

// Plugin de limitation temporelle
X_SELECT.plugins.register('timeLimit', function(instance, options = {}) {
    const { duration = 60000, message = 'Temps écoulé' } = options;
    
    setTimeout(() => {
        instance.disable();
        if (instance.wrapper) {
            instance.wrapper.title = message;
        }
    }, duration);
});

// Plugin de validation en temps réel
X_SELECT.plugins.register('liveValidation', function(instance, options = {}) {
    const { rules = {}, showErrors = true } = options;
    
    instance.on('change', () => {
        const validation = instance.validate(rules);
        
        instance.wrapper.classList.toggle('is-valid', validation.valid);
        instance.wrapper.classList.toggle('is-invalid', !validation.valid);
        
        if (showErrors && !validation.valid) {
            let errorDiv = instance.wrapper.querySelector('.validation-errors');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'validation-errors';
                instance.wrapper.appendChild(errorDiv);
            }
            errorDiv.innerHTML = validation.errors.map(err => 
                `<small class="text-danger">${err}</small>`
            ).join('<br>');
        } else {
            const errorDiv = instance.wrapper.querySelector('.validation-errors');
            if (errorDiv) errorDiv.remove();
        }
    });
});

// ======================== ALIAS ÉTENDUS ========================
const x_select_create = X_SELECT.create.bind(X_SELECT);
const x_select_ajax = X_SELECT.ajax.bind(X_SELECT);
const x_select_render = X_SELECT.render.bind(X_SELECT);
const x_select_multiple = X_SELECT.multiple.bind(X_SELECT);
const x_select_tags = X_SELECT.tags.bind(X_SELECT);
const x_select_countries = X_SELECT.countries.bind(X_SELECT);
const x_select_currencies = X_SELECT.currencies.bind(X_SELECT);
const x_select_languages = X_SELECT.languages.bind(X_SELECT);
const x_select_timezones = X_SELECT.timezones.bind(X_SELECT);
const x_select_rating = X_SELECT.rating.bind(X_SELECT);
const x_select_priority = X_SELECT.priority.bind(X_SELECT);
const x_select_status = X_SELECT.status.bind(X_SELECT);
const x_select_getInstance = X_SELECT.getInstance.bind(X_SELECT);
const x_select_destroyAll = X_SELECT.destroyAll.bind(X_SELECT);
const x_select_createPreset = X_SELECT.createPreset.bind(X_SELECT);

// ======================== CSS UTILITIES ========================
if (typeof document !== 'undefined') {
    const style = document.createElement('style');
    style.textContent = `
        /* Animations X_SELECT */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Classes utilitaires */
        .x-select-loading {
            position: relative;
        }
        
        .x-select-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 10px;
            width: 16px;
            height: 16px;
            margin-top: -8px;
            border: 2px solid #ccc;
            border-top-color: #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Validation */
        .is-valid {
            border-color: #28a745 !important;
        }
        
        .is-invalid {
            border-color: #dc3545 !important;
        }
        
        .validation-errors {
            margin-top: 5px;
        }
        
        .text-danger {
            color: #dc3545 !important;
        }
    `;
    document.head.appendChild(style);
}

// ======================== EXPORT ========================

// Export pour le navigateur
if (typeof window !== 'undefined') {
    window.X_SELECT = X_SELECT;
}

// Export pour les modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = X_SELECT;
}



//      const ajaxSelect = x_select_ajax('#my-select', {
//     url: '{{ route("categories.search") }}',
//     valueField: 'id',
//     labelField: 'nom',
//     searchField: ['nom', 'parent_id'],
//     preload: true,
//     plugins: ['remove_button'],
//     maxItems: 2,
//     //transform: (data) => data.users, // Transformer la réponse
//     fetchOptions: {
//         method: 'GET',
//         headers: {
//             'Authorization': 'Bearer token123'
//         }
//     },
//     render : {
//     option: (data, escape) => `
//         <div class="option">
//             <img src="${escape(data.image_presentation)}" alt="" style="width:32px;height:32px;border-radius:50%;margin-right:8px;">
//             <div>
//                 <strong>${escape(data.nom)}</strong>
//                 <br><small>${escape(data.parent)}</small>
//             </div>
//         </div>
//     `,
//     item: (data, escape) => `
//         <div class="item">
//             <img src="${escape(data.image_banner)}" alt="" style="width:20px;height:20px;border-radius:50%;margin-right:5px;">
//             ${escape(data.nom)}
//         </div>
//     `
// }
// });


{/* <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js" defer></script>
 */}
