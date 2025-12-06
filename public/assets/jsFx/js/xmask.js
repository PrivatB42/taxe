//const { mask } = require("./xstring");

/**
 * Bibliothèque X_MASK - Wrapper pour iMask.js uniquement
 * @version 2.1
 * @license MIT
 * @requires iMask.js (https://imask.js.org/)
 */
const X_MASK = {
    // Configuration par défaut
    defaults: {
        lazy: true,
        placeholderChar: '_',
        showMaskOnHover: true,
        showMaskOnFocus: true,
        rightAlign: false,
        returnRaw: true  // Nouvelle option: true pour valeur brute, false pour formatée
    },

    // Instances actives
    instances: new Map(),
    currentInstance: null,

    // ======================== INITIALISATION ========================

    /** Créer un masque sur un élément (alias x_mask_create) */
    create(selector, mask, options = {}) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (!element) {
            throw new Error('Élément non trouvé');
        }

        const config = { ...this.defaults, ...options };
        const elementId = element.id || `x_mask_${Date.now()}`;
        
        const instance = this._createIMaskInstance(element, mask, config);

        // Stocker la configuration
        instance._xMaskConfig = config;
        
        this.instances.set(elementId, instance);
        this.currentInstance = instance;
        element.setAttribute('data-x-mask', elementId);

        return instance;
    },

    /** Créer un masque simple (alias x_mask_simple) */
    simple(selector, pattern) {
        return this.create(selector, pattern, { lazy: false });
    },

    /** Créer un masque avec placeholder (alias x_mask_placeholder) */
    placeholder(selector, mask, placeholder = '_') {
        return this.create(selector, mask, {
            placeholderChar: placeholder,
            lazy: false
        });
    },

    // ======================== MASQUES PRÉDÉFINIS ========================

    /** Masque téléphone (alias x_mask_phone) */
    phone(selector, format = 'fr', options = {}) {
        const patterns = {
            fr: '00 00 00 00 00',
            mobile: '00 00 00 00 00',
            international: '+{33} 0 00 00 00 00',
            us: '(000) 000-0000'
        };

        const pattern = patterns[format] || format;
        return this.create(selector, pattern, options);
    },

    /** Masque date (alias x_mask_date) */
    date(selector, format = 'dd/mm/yyyy', options = {}) {
        return this.create(selector, {
            mask: Date,
            pattern: format,
            blocks: {
                dd: { mask: IMask.MaskedRange, from: 1, to: 31, maxLength: 2 },
                mm: { mask: IMask.MaskedRange, from: 1, to: 12, maxLength: 2 },
                yyyy: { mask: IMask.MaskedRange, from: 1900, to: 2099 }
            },
            format: function (date) {
                return [
                    date.getDate().toString().padStart(2, '0'),
                    (date.getMonth() + 1).toString().padStart(2, '0'),
                    date.getFullYear()
                ].join('/');
            },
            parse: function (str) {
                const parts = str.split('/');
                return new Date(parts[2], parts[1] - 1, parts[0]);
            },
            ...options
        });
    },

    /** Masque time (alias x_mask_time) */
    time(selector, format = '24h', options = {}) {
        const patterns = {
            '24h': 'HH:mm',
            '12h': 'hh:mm aa',
            seconds: 'HH:mm:ss'
        };

        const pattern = patterns[format] || format;
        return this.create(selector, {
            mask: pattern,
            blocks: {
                HH: { mask: IMask.MaskedRange, from: 0, to: 23 },
                hh: { mask: IMask.MaskedRange, from: 1, to: 12 },
                mm: { mask: IMask.MaskedRange, from: 0, to: 59 },
                ss: { mask: IMask.MaskedRange, from: 0, to: 59 },
                aa: {
                    mask: IMask.MaskedEnum,
                    enum: ['AM', 'PM']
                }
            },
            ...options
        });
    },

    /** Masque numérique (alias x_mask_number) */
    number(selector, options = {}) {
        return this.create(selector, {
            mask: Number,
            scale: options.scale || 2,
            thousandsSeparator: options.thousandsSeparator || ' ',
            padFractionalZeros: options.padFractionalZeros || true,
            normalizeZeros: options.normalizeZeros || true,
            radix: options.radix || ',',
            mapToRadix: ['.'],
            signed: options.signed || false,
            min: options.min,
            max: options.max,
            ...options
        });
    },

    /** Masque monétaire (alias x_mask_currency) */
    currency(selector, currency = 'EUR', options = {}) {
        const prefixes = {
            'EUR': '€ ',
            'USD': '$ ',
            'GBP': '£ ',
            'FCFA': 'FCFA '
        };

        return this.create(selector, {
            mask: Number,
            scale: 0,
            thousandsSeparator: ' ',
            padFractionalZeros: true,
            normalizeZeros: true,
            radix: ',',
            mapToRadix: ['.'],
            min: 0,
            prefix: ' ' + currency,
            ...options
        });
    },

    /** Masque pourcentage (alias x_mask_percent) */
    percent(selector, options = {}) {
        return this.create(selector, {
            mask: Number,
            scale: 2,
            min: 0,
            max: 100,
            radix: ',',
            mapToRadix: ['.'],
            commit: (value, masked) => masked.value + ' %',
            prepare: (str) => str.replace(' %', ''),
            ...options
        });
    },

    /** Masque email (alias x_mask_email) */
    email(selector, options = {}) {
        return this.create(selector, {
            mask: /^\S*@?\S*$/,
            ...options
        });
    },

    /** Masque IBAN (alias x_mask_iban) */
    iban(selector, options = {}) {
        return this.create(selector, {
            mask: 'aa00 0000 0000 0000 0000 00',
            definitions: {
                'a': /[A-Z]/,
                '0': /[0-9]/
            },
            prepare: str => str.toUpperCase().replace(/\s/g, ''),
            ...options
        });
    },

    /** Masque carte de crédit (alias x_mask_creditcard) */
    creditcard(selector, options = {}) {
        return this.create(selector, {
            mask: [
                { mask: '0000 0000 0000 0000' }, // Visa, MasterCard
                { mask: '0000 000000 00000' },  // American Express
                { mask: '0000 0000 0000 0000 000' } // Diners Club
            ],
            ...options
        });
    },

    /** Masque code postal (alias x_mask_postal) */
    postal(selector, country = 'fr', options = {}) {
        const patterns = {
            fr: '00000',
            us: '00000{-0000}',
            ca: 'a0a{ 0a0}',
            uk: 'aa00{ 0aa}'
        };

        const pattern = patterns[country] || patterns.fr;
        const config = country === 'ca' || country === 'uk' ? {
            definitions: {
                'a': /[A-Z]/,
                '0': /[0-9]/
            },
            prepare: str => str.toUpperCase()
        } : {};

        return this.create(selector, pattern, { ...config, ...options });
    },

    /** Masque SIRET/SIREN (alias x_mask_siret) */
    siret(selector, type = 'siret', options = {}) {
        const patterns = {
            siret: '000 000 000 00000',
            siren: '000 000 000'
        };

        return this.create(selector, patterns[type], options);
    },

    /** Masque NIR (Numéro de Sécurité Sociale) (alias x_mask_nir) */
    nir(selector, options = {}) {
        return this.create(selector, '0 00 00 00 000 000 00', options);
    },

    /** Masque IP (alias x_mask_ip) */
    ip(selector, version = 'v4', options = {}) {
        if (version === 'v4') {
            return this.create(selector, {
                mask: 'num.num.num.num',
                blocks: {
                    num: {
                        mask: IMask.MaskedRange,
                        from: 0,
                        to: 255,
                        maxLength: 3
                    }
                },
                ...options
            });
        } else {
            return this.create(selector, {
                mask: 'HHHH:HHHH:HHHH:HHHH:HHHH:HHHH:HHHH:HHHH',
                definitions: {
                    'H': /[0-9A-Fa-f]/
                },
                prepare: str => str.toLowerCase(),
                ...options
            });
        }
    },

    /** Masque MAC Address (alias x_mask_mac) */
    mac(selector, separator = ':', options = {}) {
        const pattern = separator === ':' ? 
            'HH:HH:HH:HH:HH:HH' : 'HH-HH-HH-HH-HH-HH';

        return this.create(selector, {
            mask: pattern,
            definitions: {
                'H': /[0-9A-Fa-f]/
            },
            prepare: str => str.toUpperCase(),
            ...options
        });
    },

    /** Masque couleur hexadécimale (alias x_mask_color) */
    color(selector, includeHash = true, options = {}) {
        const pattern = includeHash ? '#HHHHHH' : 'HHHHHH';

        return this.create(selector, {
            mask: pattern,
            definitions: {
                'H': /[0-9A-Fa-f]/
            },
            prepare: str => includeHash ? str.toUpperCase() : str.toUpperCase().replace('#', ''),
            ...options
        });
    },

    /** Masque plaque d'immatriculation (alias x_mask_license) */
    license(selector, format = 'new', options = {}) {
        const patterns = {
            new: 'AA-000-AA',    // Format 2009+
            old: '0000 AA 00'    // Ancien format
        };

        return this.create(selector, {
            mask: patterns[format],
            definitions: {
                'A': /[A-Z]/,
                '0': /[0-9]/
            },
            prepare: str => str.toUpperCase(),
            ...options
        });
    },

    /** Masque UUID (alias x_mask_uuid) */
    uuid(selector, options = {}) {
        return this.create(selector, {
            mask: 'HHHHHHHH-HHHH-HHHH-HHHH-HHHHHHHHHHHH',
            definitions: {
                'H': /[0-9A-Fa-f]/
            },
            prepare: str => str.toLowerCase(),
            ...options
        });
    },

    /** Masque coordonnées GPS (alias x_mask_gps) */
    gps(selector, type = 'decimal', options = {}) {
        if (type === 'decimal') {
            return this.create(selector, {
                mask: Number,
                scale: 6,
                min: -180,
                max: 180,
                radix: '.',
                signed: true,
                ...options
            });
        } else {
            // DMS format
            return this.create(selector, {
                mask: 'num°num\'num"',
                blocks: {
                    num: {
                        mask: IMask.MaskedRange,
                        from: 0,
                        to: 360
                    }
                },
                ...options
            });
        }
    },

    /** Masque durée (alias x_mask_duration) */
    duration(selector, format = 'hms', options = {}) {
        const patterns = {
            hms: 'HH:mm:ss',
            hm: 'HH:mm',
            ms: 'mm:ss'
        };

        return this.create(selector, {
            mask: patterns[format],
            blocks: {
                HH: { mask: IMask.MaskedRange, from: 0, to: 99 },
                mm: { mask: IMask.MaskedRange, from: 0, to: 59 },
                ss: { mask: IMask.MaskedRange, from: 0, to: 59 }
            },
            ...options
        });
    },

    // ======================== CONFIGURATION ========================

    /** Activer/désactiver la saisie paresseuse (alias x_mask_lazy) */
    lazy(selector, enable = true) {
        const instance = this.getInstance(selector);
        if (instance && instance.updateOptions) {
            instance.updateOptions({ lazy: enable });
        }
        return this;
    },

    /** Définir le placeholder (alias x_mask_setPlaceholder) */
    setPlaceholder(selector, char = '_') {
        const instance = this.getInstance(selector);
        if (instance && instance.updateOptions) {
            instance.updateOptions({ placeholderChar: char });
        }
        return this;
    },

    /** Aligner à droite (alias x_mask_rightAlign) */
    rightAlign(selector, enable = true) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (element) {
            element.style.textAlign = enable ? 'right' : 'left';
        }
        return this;
    },

    /** Définir des définitions personnalisées (alias x_mask_definitions) */
    definitions(selector, defs) {
        const instance = this.getInstance(selector);
        if (instance && instance.updateOptions) {
            instance.updateOptions({ definitions: defs });
        }
        return this;
    },

    /** Définir un préprocesseur (alias x_mask_prepare) */
    prepare(selector, prepareFn) {
        const instance = this.getInstance(selector);
        if (instance && instance.updateOptions) {
            instance.updateOptions({ prepare: prepareFn });
        }
        return this;
    },

    /** Définir des blocs (alias x_mask_blocks) */
    blocks(selector, blocksConfig) {
        const instance = this.getInstance(selector);
        if (instance && instance.updateOptions) {
            instance.updateOptions({ blocks: blocksConfig });
        }
        return this;
    },

    /** Définir une transformation personnalisée (alias x_mask_transform) */
    transform(selector, transformFn) {
        const instance = this.getInstance(selector);
        if (instance && instance.updateOptions) {
            instance.updateOptions({ 
                prepare: transformFn 
            });
        }
        return this;
    },

    /** Définir des options avancées (alias x_mask_configure) */
    configure(selector, config) {
        const instance = this.getInstance(selector);
        if (instance && instance.updateOptions) {
            instance.updateOptions(config);
        }
        return this;
    },

    /** Forcer la casse (alias x_mask_forceCase) */
    forceCase(selector, caseType = 'upper') {
        const transformers = {
            upper: str => str.toUpperCase(),
            lower: str => str.toLowerCase(),
            title: str => str.replace(/\w\S*/g, txt => 
                txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()
            )
        };

        return this.transform(selector, transformers[caseType] || transformers.upper);
    },

    /** Changer le mode de retour (alias x_mask_setReturnMode) */
    setReturnMode(selector, returnRaw = true) {
        const instance = this.getInstance(selector);
        if (instance && instance._xMaskConfig) {
            instance._xMaskConfig.returnRaw = returnRaw;
        }
        return this;
    },

    /** Alterner entre les modes (alias x_mask_toggleMode) */
    toggleMode(selector) {
        const instance = this.getInstance(selector);
        if (instance && instance._xMaskConfig) {
            instance._xMaskConfig.returnRaw = !instance._xMaskConfig.returnRaw;
        }
        return this;
    },

    // ======================== VALIDATION ========================

    /** Valider la valeur (alias x_mask_validate) */
    validate(selector) {
        const instance = this.getInstance(selector);
        if (!instance) return false;

        return instance.masked && instance.masked.isComplete;
    },

    /** Vérifier si complet (alias x_mask_isComplete) */
    isComplete(selector) {
        const instance = this.getInstance(selector);
        if (!instance) return false;

        return instance.masked && instance.masked.isComplete;
    },

    /** Vérifier si valide selon regex (alias x_mask_isValidPattern) */
    isValidPattern(selector, pattern) {
        const value = this.getRawValue(selector);
        const regex = new RegExp(pattern);
        return regex.test(value);
    },

    /** Valider longueur (alias x_mask_validateLength) */
    validateLength(selector, minLength = 0, maxLength = Infinity) {
        const value = this.getRawValue(selector);
        return value.length >= minLength && value.length <= maxLength;
    },

    /** Valider avec une fonction personnalisée (alias x_mask_validateCustom) */
    validateCustom(selector, validatorFn) {
        const value = this.getRawValue(selector);
        const formattedValue = this.getFormattedValue(selector);
        return validatorFn(value, formattedValue);
    },

    /** Valider format email (alias x_mask_validateEmail) */
    validateEmail(selector) {
        const value = this.getRawValue(selector);
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(value);
    },

    /** Valider numéro de téléphone (alias x_mask_validatePhone) */
    validatePhone(selector, format = 'fr') {
        const value = this.getRawValue(selector);
        const patterns = {
            fr: /^[0-9]{10}$/,
            mobile: /^[0-9]{10}$/,
            international: /^\+[1-9]\d{1,14}$/,
            us: /^[0-9]{10}$/
        };
        
        const pattern = patterns[format] || patterns.fr;
        return pattern.test(value);
    },

    /** Valider date (alias x_mask_validateDate) */
    validateDate(selector, minDate = null, maxDate = null) {
        const typedValue = this.getTypedValue(selector);
        if (!(typedValue instanceof Date) || isNaN(typedValue)) return false;
        
        if (minDate && typedValue < new Date(minDate)) return false;
        if (maxDate && typedValue > new Date(maxDate)) return false;
        
        return true;
    },

    /** Valider nombre dans une plage (alias x_mask_validateRange) */
    validateRange(selector, min = -Infinity, max = Infinity) {
        const value = this.getTypedValue(selector);
        const numValue = typeof value === 'number' ? value : parseFloat(value);
        
        if (isNaN(numValue)) return false;
        return numValue >= min && numValue <= max;
    },

    // ======================== VALEURS ========================

    /** Obtenir la valeur selon la préférence returnRaw */
    getValue(selector, forceType = null) {
        const instance = this.getInstance(selector);
        if (!instance) return '';

        const config = instance._xMaskConfig || this.defaults;
        const returnRaw = forceType !== null ? forceType : config.returnRaw;

        return returnRaw ? instance.unmaskedValue : instance.value;
    },

    /** Obtenir la valeur brute (alias x_mask_getRawValue) */
    getRawValue(selector) {
        return this.getValue(selector, true);
    },

    /** Obtenir la valeur formatée (alias x_mask_getFormattedValue) */
    getFormattedValue(selector) {
        return this.getValue(selector, false);
    },

    /** Définir la valeur (alias x_mask_setValue) */
    setValue(selector, value) {
        const instance = this.getInstance(selector);
        
        if (instance) {
            instance.value = value;
        }
        return this;
    },

    /** Vider la valeur (alias x_mask_clear) */
    clear(selector) {
        return this.setValue(selector, '');
    },

    /** Obtenir la valeur typée (alias x_mask_getTypedValue) */
    getTypedValue(selector) {
        const instance = this.getInstance(selector);
        if (!instance) return null;

        const config = instance._xMaskConfig || this.defaults;
        
        if (config.returnRaw) {
            // Pour les valeurs brutes, convertir si nécessaire
            const rawValue = instance.unmaskedValue;
            
            // Si c'est un masque numérique, parser le nombre
            if (instance.masked && instance.masked.mask === Number) {
                return parseFloat(rawValue.replace(/[^\d.,-]/g, '').replace(',', '.'));
            }
            
            return rawValue;
        }
        
        return instance.typedValue !== undefined ? instance.typedValue : instance.value;
    },

    /** Obtenir les métadonnées du masque (alias x_mask_getMetadata) */
    getMetadata(selector) {
        const instance = this.getInstance(selector);
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        const config = instance ? (instance._xMaskConfig || this.defaults) : this.defaults;
        
        return {
            isValid: this.validate(selector),
            isComplete: this.isComplete(selector),
            rawValue: this.getRawValue(selector),
            formattedValue: this.getFormattedValue(selector),
            typedValue: this.getTypedValue(selector),
            preferredValue: this.getValue(selector), // Valeur selon returnRaw
            returnRaw: config.returnRaw,
            length: this.getRawValue(selector).length,
            element: element,
            instance: instance,
            hasValue: this.getRawValue(selector).length > 0
        };
    },

    /** Copier la valeur d'un champ à un autre (alias x_mask_copyValue) */
    copyValue(sourceSelector, targetSelector) {
        const sourceValue = this.getRawValue(sourceSelector);
        return this.setValue(targetSelector, sourceValue);
    },

    /** Échanger les valeurs de deux champs (alias x_mask_swapValues) */
    swapValues(selector1, selector2) {
        const value1 = this.getRawValue(selector1);
        const value2 = this.getRawValue(selector2);
        
        this.setValue(selector1, value2);
        this.setValue(selector2, value1);
        
        return this;
    },

    /** Appliquer une transformation sur la valeur (alias x_mask_applyTransform) */
    applyTransform(selector, transformFn) {
        const currentValue = this.getRawValue(selector);
        const transformedValue = transformFn(currentValue);
        return this.setValue(selector, transformedValue);
    },

    // ======================== ÉVÉNEMENTS ========================

    /** Ajouter un écouteur (alias x_mask_on) */
    on(selector, event, callback) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        const instance = this.getInstance(selector);

        if (element && instance) {
            // Événements iMask
            if (['accept', 'complete'].includes(event)) {
                instance.on(event, callback);
            }
            // Événements DOM standards
            else {
                element.addEventListener(event, callback);
            }
        }
        return this;
    },

    /** Événement onChange (alias x_mask_onChange) */
    onChange(selector, callback) {
        return this.on(selector, 'change', callback);
    },

    /** Événement onAccept (alias x_mask_onAccept) */
    onAccept(selector, callback) {
        return this.on(selector, 'accept', callback);
    },

    /** Événement onComplete (alias x_mask_onComplete) */
    onComplete(selector, callback) {
        return this.on(selector, 'complete', callback);
    },

    /** Événement onFocus (alias x_mask_onFocus) */
    onFocus(selector, callback) {
        return this.on(selector, 'focus', callback);
    },

    /** Événement onBlur (alias x_mask_onBlur) */
    onBlur(selector, callback) {
        return this.on(selector, 'blur', callback);
    },

    /** Événement onKeyPress (alias x_mask_onKeyPress) */
    onKeyPress(selector, callback) {
        return this.on(selector, 'keypress', callback);
    },

    /** Événement onKeyDown (alias x_mask_onKeyDown) */
    onKeyDown(selector, callback) {
        return this.on(selector, 'keydown', callback);
    },

    /** Événement onPaste (alias x_mask_onPaste) */
    onPaste(selector, callback) {
        return this.on(selector, 'paste', callback);
    },

    /** Événement personnalisé conditionnel (alias x_mask_onCondition) */
    onCondition(selector, condition, callback) {
        return this.on(selector, 'accept', () => {
            if (condition(this.getRawValue(selector), this.getFormattedValue(selector))) {
                callback();
            }
        });
    },

    /** Débounce sur les événements (alias x_mask_onDebounce) */
    onDebounce(selector, event, callback, delay = 300) {
        let timeout;
        return this.on(selector, event, () => {
            clearTimeout(timeout);
            timeout = setTimeout(callback, delay);
        });
    },

    // ======================== CONTRÔLE ========================

    /** Activer le masque (alias x_mask_enable) */
    enable(selector) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (element) {
            element.disabled = false;
        }
        return this;
    },

    /** Désactiver le masque (alias x_mask_disable) */
    disable(selector) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (element) {
            element.disabled = true;
        }
        return this;
    },

    /** Détruire le masque (alias x_mask_destroy) */
    destroy(selector) {
        const instance = this.getInstance(selector);
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        
        if (instance) {
            instance.destroy();
            
            const elementId = element.getAttribute('data-x-mask');
            if (elementId) {
                this.instances.delete(elementId);
                element.removeAttribute('data-x-mask');
            }
        }
        return this;
    },

    /** Mettre à jour le masque (alias x_mask_update) */
    update(selector, newMask, options = {}) {
        this.destroy(selector);
        return this.create(selector, newMask, options);
    },

    /** Réinitialiser le masque (alias x_mask_reset) */
    reset(selector) {
        this.clear(selector);
        const instance = this.getInstance(selector);
        if (instance && instance.updateOptions) {
            instance.updateOptions(this.defaults);
        }
        return this;
    },

    /** Sauvegarder l'état du masque (alias x_mask_saveState) */
    saveState(selector, stateName = 'default') {
        const metadata = this.getMetadata(selector);
        if (!this._states) this._states = new Map();
        
        this._states.set(`${this._getElementId(selector)}_${stateName}`, {
            value: metadata.rawValue,
            config: metadata.instance ? metadata.instance.masked : null
        });
        
        return this;
    },

    /** Restaurer l'état du masque (alias x_mask_restoreState) */
    restoreState(selector, stateName = 'default') {
        if (!this._states) return this;
        
        const state = this._states.get(`${this._getElementId(selector)}_${stateName}`);
        if (state) {
            this.setValue(selector, state.value);
        }
        
        return this;
    },

    /** Créer un groupe de masques (alias x_mask_group) */
    group(selectors, commonOptions = {}) {
        const group = [];
        selectors.forEach(config => {
            if (typeof config === 'string') {
                group.push(this.create(config, '000', commonOptions));
            } else {
                group.push(this.create(config.selector, config.mask, { ...commonOptions, ...config.options }));
            }
        });
        
        return {
            validate: () => group.every(instance => this.validate(instance.element)),
            clear: () => { group.forEach(instance => this.clear(instance.element)); return this; },
            enable: () => { group.forEach(instance => this.enable(instance.element)); return this; },
            disable: () => { group.forEach(instance => this.disable(instance.element)); return this; },
            destroy: () => { group.forEach(instance => this.destroy(instance.element)); return this; }
        };
    },

    /** Prévisualiser le masque (alias x_mask_preview) */
    preview(mask, sampleValue = '') {
        const tempElement = document.createElement('input');
        const tempInstance = this.create(tempElement, mask);
        
        if (sampleValue) {
            this.setValue(tempElement, sampleValue);
        }
        
        const preview = {
            formatted: this.getFormattedValue(tempElement),
            raw: this.getRawValue(tempElement),
            pattern: mask
        };
        
        this.destroy(tempElement);
        return preview;
    },

    // ======================== UTILITAIRES ========================

    /** Obtenir l'instance (alias x_mask_getInstance) */
    getInstance(selector) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (!element) return null;

        const elementId = element.getAttribute('data-x-mask');
        return elementId ? this.instances.get(elementId) : this.currentInstance;
    },

    /** Cloner un masque (alias x_mask_clone) */
    clone(sourceSelector, targetSelector) {
        const sourceInstance = this.getInstance(sourceSelector);
        if (!sourceInstance) return null;

        // Récupérer la configuration originale
        const config = sourceInstance.masked ? { ...sourceInstance.masked } : {};

        return this.create(targetSelector, config.mask || config, config);
    },

    /** Convertir format (alias x_mask_convertFormat) */
    convertFormat(value, fromFormat, toFormat) {
        // Logique de conversion entre formats
        if (fromFormat === 'date' && toFormat === 'iso') {
            const parts = value.split('/');
            return `${parts[2]}-${parts[1]}-${parts[0]}`;
        }
        if (fromFormat === 'iso' && toFormat === 'date') {
            const parts = value.split('-');
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }
        return value;
    },

    /** Formateur personnalisé (alias x_mask_customFormatter) */
    customFormatter(value, pattern, options = {}) {
        const tempElement = document.createElement('input');
        const tempInstance = this.create(tempElement, pattern, options);
        
        this.setValue(tempElement, value);
        const formatted = this.getFormattedValue(tempElement);
        
        this.destroy(tempElement);
        return formatted;
    },

    /** Analyser un masque (alias x_mask_analyze) */
    analyze(mask) {
        return {
            type: typeof mask,
            isRegex: mask instanceof RegExp,
            isObject: typeof mask === 'object' && !(mask instanceof RegExp),
            isString: typeof mask === 'string',
            isFunction: typeof mask === 'function',
            length: typeof mask === 'string' ? mask.length : null,
            hasBlocks: typeof mask === 'string' && /[0-9]/.test(mask),
            hasLetters: typeof mask === 'string' && /[A-Za-z]/.test(mask),
            hasSpecialChars: typeof mask === 'string' && /[^A-Za-z0-9]/.test(mask)
        };
    },

    /** Statistiques d'utilisation (alias x_mask_getStats) */
    getStats() {
        return {
            totalInstances: this.instances.size,
            activeInstances: Array.from(this.instances.values()).filter(i => i && !i.destroyed).length,
            instanceTypes: { imask: this.instances.size }
        };
    },

    /** Débugger un masque (alias x_mask_debug) */
    debug(selector) {
        const instance = this.getInstance(selector);
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        
        return {
            element: element,
            instance: instance,
            metadata: this.getMetadata(selector),
            config: instance ? instance.masked : null,
            events: this._getElementEvents(element)
        };
    },

    // ======================== MÉTHODES PRIVÉES ========================

    _createIMaskInstance(element, mask, options) {
        if (typeof IMask === 'undefined') {
            throw new Error('iMask.js n\'est pas chargé');
        }
        return IMask(element, typeof mask === 'object' ? mask : { mask, ...options });
    },

    _getElementId(selector) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        return element ? (element.id || element.getAttribute('data-x-mask') || 'unknown') : 'unknown';
    },

    _getElementEvents(element) {
        if (!element) return [];
        
        const events = [];
        const eventTypes = ['input', 'change', 'focus', 'blur', 'keypress', 'keydown', 'paste'];
        
        eventTypes.forEach(type => {
            if (element[`on${type}`]) {
                events.push(type);
            }
        });
        
        return events;
    }
};

// ======================== ALIAS ========================
const x_mask_create = X_MASK.create.bind(X_MASK);
const x_mask_simple = X_MASK.simple.bind(X_MASK);
const x_mask_placeholder = X_MASK.placeholder.bind(X_MASK);
const x_mask_phone = X_MASK.phone.bind(X_MASK);
const x_mask_date = X_MASK.date.bind(X_MASK);
const x_mask_time = X_MASK.time.bind(X_MASK);
const x_mask_number = X_MASK.number.bind(X_MASK);
const x_mask_currency = X_MASK.currency.bind(X_MASK);
const x_mask_percent = X_MASK.percent.bind(X_MASK);
const x_mask_email = X_MASK.email.bind(X_MASK);
const x_mask_iban = X_MASK.iban.bind(X_MASK);
const x_mask_creditcard = X_MASK.creditcard.bind(X_MASK);
const x_mask_postal = X_MASK.postal.bind(X_MASK);
const x_mask_siret = X_MASK.siret.bind(X_MASK);
const x_mask_nir = X_MASK.nir.bind(X_MASK);
const x_mask_ip = X_MASK.ip.bind(X_MASK);
const x_mask_mac = X_MASK.mac.bind(X_MASK);
const x_mask_color = X_MASK.color.bind(X_MASK);
const x_mask_license = X_MASK.license.bind(X_MASK);
const x_mask_uuid = X_MASK.uuid.bind(X_MASK);
const x_mask_gps = X_MASK.gps.bind(X_MASK);
const x_mask_duration = X_MASK.duration.bind(X_MASK);
const x_mask_lazy = X_MASK.lazy.bind(X_MASK);
const x_mask_setPlaceholder = X_MASK.setPlaceholder.bind(X_MASK);
const x_mask_rightAlign = X_MASK.rightAlign.bind(X_MASK);
const x_mask_definitions = X_MASK.definitions.bind(X_MASK);
const x_mask_prepare = X_MASK.prepare.bind(X_MASK);
const x_mask_blocks = X_MASK.blocks.bind(X_MASK);
const x_mask_transform = X_MASK.transform.bind(X_MASK);
const x_mask_configure = X_MASK.configure.bind(X_MASK);
const x_mask_forceCase = X_MASK.forceCase.bind(X_MASK);
const x_mask_setReturnMode = X_MASK.setReturnMode.bind(X_MASK);
const x_mask_toggleMode = X_MASK.toggleMode.bind(X_MASK);
const x_mask_validate = X_MASK.validate.bind(X_MASK);
const x_mask_isComplete = X_MASK.isComplete.bind(X_MASK);
const x_mask_isValidPattern = X_MASK.isValidPattern.bind(X_MASK);
const x_mask_validateLength = X_MASK.validateLength.bind(X_MASK);
const x_mask_validateCustom = X_MASK.validateCustom.bind(X_MASK);
const x_mask_validateEmail = X_MASK.validateEmail.bind(X_MASK);
const x_mask_validatePhone = X_MASK.validatePhone.bind(X_MASK);
const x_mask_validateDate = X_MASK.validateDate.bind(X_MASK);
const x_mask_validateRange = X_MASK.validateRange.bind(X_MASK);
const x_mask_getValue = X_MASK.getValue.bind(X_MASK);
const x_mask_getRawValue = X_MASK.getRawValue.bind(X_MASK);
const x_mask_getFormattedValue = X_MASK.getFormattedValue.bind(X_MASK);
const x_mask_setValue = X_MASK.setValue.bind(X_MASK);
const x_mask_clear = X_MASK.clear.bind(X_MASK);
const x_mask_getTypedValue = X_MASK.getTypedValue.bind(X_MASK);
const x_mask_getMetadata = X_MASK.getMetadata.bind(X_MASK);
const x_mask_copyValue = X_MASK.copyValue.bind(X_MASK);
const x_mask_swapValues = X_MASK.swapValues.bind(X_MASK);
const x_mask_applyTransform = X_MASK.applyTransform.bind(X_MASK);
const x_mask_on = X_MASK.on.bind(X_MASK);
const x_mask_onChange = X_MASK.onChange.bind(X_MASK);
const x_mask_onAccept = X_MASK.onAccept.bind(X_MASK);
const x_mask_onComplete = X_MASK.onComplete.bind(X_MASK);
const x_mask_onFocus = X_MASK.onFocus.bind(X_MASK);
const x_mask_onBlur = X_MASK.onBlur.bind(X_MASK);
const x_mask_onKeyPress = X_MASK.onKeyPress.bind(X_MASK);
const x_mask_onKeyDown = X_MASK.onKeyDown.bind(X_MASK);
const x_mask_onPaste = X_MASK.onPaste.bind(X_MASK);
const x_mask_onCondition = X_MASK.onCondition.bind(X_MASK);
const x_mask_onDebounce = X_MASK.onDebounce.bind(X_MASK);
const x_mask_enable = X_MASK.enable.bind(X_MASK);
const x_mask_disable = X_MASK.disable.bind(X_MASK);
const x_mask_destroy = X_MASK.destroy.bind(X_MASK);
const x_mask_update = X_MASK.update.bind(X_MASK);
const x_mask_reset = X_MASK.reset.bind(X_MASK);
const x_mask_saveState = X_MASK.saveState.bind(X_MASK);
const x_mask_restoreState = X_MASK.restoreState.bind(X_MASK);
const x_mask_group = X_MASK.group.bind(X_MASK);
const x_mask_preview = X_MASK.preview.bind(X_MASK);
const x_mask_getInstance = X_MASK.getInstance.bind(X_MASK);
const x_mask_clone = X_MASK.clone.bind(X_MASK);
const x_mask_convertFormat = X_MASK.convertFormat.bind(X_MASK);
const x_mask_customFormatter = X_MASK.customFormatter.bind(X_MASK);
const x_mask_analyze = X_MASK.analyze.bind(X_MASK);
const x_mask_getStats = X_MASK.getStats.bind(X_MASK);
const x_mask_debug = X_MASK.debug.bind(X_MASK);

// Export pour le navigateur
if (typeof window !== 'undefined') {
    window.X_MASK = X_MASK;
}

// Export pour les modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = X_MASK;
}

/* 
EXEMPLES D'UTILISATION AVEC returnRaw :

// ============ MASQUES AVEC VALEURS BRUTES ============

// Téléphone français - retourne "0123456789" (valeur brute)
x_mask_phone('#phone', 'fr', { returnRaw: true })
    .onAccept(() => {
        const rawPhone = x_mask_getRawValue('#phone'); // "0123456789"
        const preferredPhone = x_mask_getValue('#phone'); // "0123456789" (selon returnRaw)
        console.log('Téléphone brut:', rawPhone);
    });

// Monétaire avec valeur brute pour le traitement
x_mask_currency('#price', 'EUR', { returnRaw: true })
    .onAccept(() => {
        const rawAmount = x_mask_getRawValue('#price'); // "1500000"
        const numericAmount = parseFloat(rawAmount);
        console.log('Montant numérique:', numericAmount);
        
        // Pour affichage formaté quand même
        const formatted = x_mask_getFormattedValue('#price'); // "1 500 000,00 €"
        document.getElementById('price-display').textContent = formatted;
    });

// Monétaire avec valeur formatée pour l'affichage
x_mask_currency('#display-price', 'EUR', { returnRaw: false })
    .onAccept(() => {
        const formatted = x_mask_getValue('#display-price'); // "1 500 000,00 €"
        console.log('Affichage:', formatted);
    });

// Date avec valeur brute
x_mask_date('#birthday', 'dd/mm/yyyy', { returnRaw: true })
    .onAccept(() => {
        const rawDate = x_mask_getRawValue('#birthday'); // "31121990"
        console.log('Date brute:', rawDate);
        
        // Mais on peut aussi avoir la date typée
        const dateObj = x_mask_getTypedValue('#birthday'); // Date object
        console.log('Date objet:', dateObj);
    });

// ============ CHANGEMENT DYNAMIQUE DE MODE ============

const moneyField = x_mask_currency('#amount', 'EUR', { returnRaw: true });

// Mode brut pour le traitement
document.getElementById('calculate-btn').addEventListener('click', () => {
    const rawValue = x_mask_getRawValue('#amount'); // "1500000"
    const total = parseFloat(rawValue) * 1.2;
    console.log('Total TTC:', total);
});

// Mode formaté pour l'affichage
document.getElementById('show-formatted').addEventListener('click', () => {
    x_mask_setReturnMode('#amount', false);
    const formatted = x_mask_getValue('#amount'); // "1 500 000,00 €"
    alert('Valeur formatée: ' + formatted);
});

// Retour en mode brut
document.getElementById('show-raw').addEventListener('click', () => {
    x_mask_setReturnMode('#amount', true);
    const raw = x_mask_getValue('#amount'); // "1500000"
    alert('Valeur brute: ' + raw);
});

// Alterner entre les modes
document.getElementById('toggle-mode').addEventListener('click', () => {
    x_mask_toggleMode('#amount');
    const currentValue = x_mask_getValue('#amount');
    alert('Valeur actuelle: ' + currentValue);
});

// ============ ENVOI AU SERVEUR ============

document.getElementById('submit-form').addEventListener('submit', (e) => {
    e.preventDefault();
    
    // Toujours utiliser les valeurs brutes pour l'envoi
    const formData = {
        price: x_mask_getRawValue('#price'), // "1500000"
        phone: x_mask_getRawValue('#phone'),  // "0123456789"
        amount: x_mask_getRawValue('#amount') // "50000"
    };
    
    console.log('Données envoyées:', formData);
    
    fetch('/api/submit', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    });
});

// ============ VALIDATION AVEC VALEURS BRUTES ============

x_mask_currency('#salary', 'EUR', { returnRaw: true })
    .onAccept(() => {
        const rawSalary = x_mask_getRawValue('#salary');
        const numericSalary = parseFloat(rawSalary);
        
        if (numericSalary > 100000) {
            console.log('Salaire élevé détecté:', numericSalary);
        }
    });

x_mask_phone('#mobile', 'fr', { returnRaw: true })
    .onBlur(() => {
        const phone = x_mask_getRawValue('#mobile');
        if (phone.length === 10 && phone.startsWith('06')) {
            console.log('Numéro mobile valide');
        }
    });

// ============ AFFICHAGE FORMATÉ AVEC TRAITEMENT BRUT ============

// Pour l'UI, on veut l'affichage formaté
x_mask_currency('#user-amount', 'EUR', { returnRaw: false });

// Mais pour les calculs, on utilise la valeur brute
document.getElementById('calculate').addEventListener('click', () => {
    const rawValue = x_mask_getRawValue('#user-amount'); // "1500000"
    const formattedValue = x_mask_getFormattedValue('#user-amount'); // "1 500 000,00 €"
    
    const result = parseFloat(rawValue) * 1.2;
    document.getElementById('result').textContent = 
        `Résultat: ${result.toLocaleString()} € (basé sur: ${formattedValue})`;
});

// ============ MÉTADONNÉES COMPLÈTES ============

x_mask_currency('#example', 'EUR', { returnRaw: true })
    .onAccept(() => {
        const metadata = x_mask_getMetadata('#example');
        console.log('Métadonnées:', {
            brut: metadata.rawValue,        // "1500000"
            formaté: metadata.formattedValue, // "1 500 000,00 €"
            préféré: metadata.preferredValue, // "1500000" (selon returnRaw)
            mode: metadata.returnRaw ? 'brut' : 'formaté',
            valide: metadata.isValid,
            complet: metadata.isComplete
        });
    });

// ============ CONVERSION MANUELLE ============

function formatToRaw(formattedValue) {
    // Supprimer tous les caractères non numériques sauf , et .
    return formattedValue.replace(/[^\d,.-]/g, '').replace(',', '.');
}

function rawToFormatted(rawValue, locale = 'fr-FR', currency = 'EUR') {
    const number = parseFloat(rawValue);
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency
    }).format(number);
}

// Utilisation
const raw = x_mask_getRawValue('#price'); // "1500000"
const formatted = rawToFormatted(raw); // "1 500 000,00 €"

const formattedInput = x_mask_getFormattedValue('#price'); // "1 500 000,00 €"
const rawConverted = formatToRaw(formattedInput); // "1500000"

*/