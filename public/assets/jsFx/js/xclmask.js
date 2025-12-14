/**
 * Bibliothèque X_CLMASK - Wrapper simple pour Cleave.js
 * @version 1.0
 * @license MIT
 * @requires Cleave.js (https://nosir.github.io/cleave.js/)
 */

const X_CLMASK = {
    // Configuration par défaut
    defaults: {
        delimiter: ' ',
        numericOnly: false,
        uppercase: false,
        lowercase: false
    },

    // Instances actives
    instances: new Map(),

    // ======================== CORE ========================

    /** Créer un masque (alias x_clmask_create) */
    create(selector, options = {}) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (!element) {
            throw new Error('Élément non trouvé');
        }

        const config = { ...this.defaults, ...options };
        const elementId = element.id || `x_clmask_${Date.now()}`;
        
        const instance = new Cleave(element, config);
        
        this.instances.set(elementId, instance);
        element.setAttribute('data-x-clmask', elementId);

        return instance;
    },

    /** Détruire un masque (alias x_clmask_destroy) */
    destroy(selector) {
        const instance = this.getInstance(selector);
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        
        if (instance) {
            instance.destroy();
            
            const elementId = element.getAttribute('data-x-clmask');
            if (elementId) {
                this.instances.delete(elementId);
                element.removeAttribute('data-x-clmask');
            }
        }
        return this;
    },

    /** Obtenir une instance (alias x_clmask_getInstance) */
    getInstance(selector) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (!element) return null;

        const elementId = element.getAttribute('data-x-clmask');
        return elementId ? this.instances.get(elementId) : null;
    },

    // ======================== MASQUES PRÉDÉFINIS ========================

    /** Masque téléphone (alias x_clmask_phone) */
    phone(selector, format = 'fr', options = {}) {
        const configs = {
            fr: { blocks: [2, 2, 2, 2, 2], delimiter: ' ', numericOnly: true },
            mobile: { blocks: [2, 2, 2, 2, 2], delimiter: ' ', numericOnly: true },
            us: { phone: true, phoneRegionCode: 'US' },
            international: { blocks: [3, 1, 2, 2, 2, 2], delimiter: ' ', numericOnly: true, prefix: '+' }
        };

        const config = { ...configs[format] || configs.fr, ...options };
        return this.create(selector, config);
    },

    /** Masque date (alias x_clmask_date) */
    date(selector, format = 'dd/mm/yyyy', options = {}) {
        const config = {
            date: true,
            datePattern: format.split('').map(c => c.toLowerCase()),
            delimiter: '/',
            ...options
        };

        return this.create(selector, config);
    },

    /** Masque numérique (alias x_clmask_number) */
    number(selector, options = {}) {
        const config = {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalMark: ',',
            delimiter: ' ',
            ...options
        };

        return this.create(selector, config);
    },

    /** Masque monétaire (alias x_clmask_currency) */
    currency(selector, currency = 'EUR', options = {}) {
        const prefixes = {
            'EUR': '€ ',
            'USD': '$ ',
            'GBP': '£ '
        };

        const config = {
            numeral: true,
            prefix: prefixes[currency] || currency + ' ',
            numeralDecimalScale: 2,
            numeralThousandsGroupStyle: 'thousand',
            ...options
        };

        return this.create(selector, config);
    },

    /** Masque pourcentage (alias x_clmask_percent) */
    percent(selector, options = {}) {
        const config = {
            numeral: true,
            suffix: ' %',
            numeralDecimalScale: 2,
            ...options
        };

        return this.create(selector, config);
    },

    /** Masque carte de crédit (alias x_clmask_creditcard) */
    creditcard(selector, options = {}) {
        const config = {
            creditCard: true,
            ...options
        };

        return this.create(selector, config);
    },

    /** Masque IBAN (alias x_clmask_iban) */
    iban(selector, options = {}) {
        const config = {
            blocks: [4, 4, 4, 4, 4, 4, 4],
            delimiter: ' ',
            uppercase: true,
            ...options
        };

        return this.create(selector, config);
    },

    /** Masque code postal français (alias x_clmask_postal) */
    postal(selector, country = 'fr', options = {}) {
        const configs = {
            fr: { blocks: [5], numericOnly: true },
            us: { blocks: [5, 4], delimiter: '-', numericOnly: true },
            ca: { blocks: [3, 3], delimiter: ' ', uppercase: true }
        };

        const config = { ...configs[country] || configs.fr, ...options };
        return this.create(selector, config);
    },

    /** Masque SIRET/SIREN (alias x_clmask_siret) */
    siret(selector, type = 'siret', options = {}) {
        const configs = {
            siret: { blocks: [3, 3, 3, 5], delimiter: ' ', numericOnly: true },
            siren: { blocks: [3, 3, 3], delimiter: ' ', numericOnly: true }
        };

        const config = { ...configs[type], ...options };
        return this.create(selector, config);
    },

    /** Masque NIR (Numéro de Sécurité Sociale) (alias x_clmask_nir) */
    nir(selector, options = {}) {
        const config = {
            blocks: [1, 2, 2, 2, 3, 3, 2],
            delimiter: ' ',
            numericOnly: true,
            ...options
        };

        return this.create(selector, config);
    },

    /** Masque heure (alias x_clmask_time) */
    time(selector, format = '24h', options = {}) {
        const configs = {
            '24h': { time: true, timePattern: ['h', 'm'] },
            '12h': { time: true, timePattern: ['h', 'm'], timeFormat: '12' },
            'seconds': { time: true, timePattern: ['h', 'm', 's'] }
        };

        const config = { ...configs[format] || configs['24h'], ...options };
        return this.create(selector, config);
    },

    /** Masque numéro de téléphone avec indicatif (alias x_clmask_phone_intl) */
    phone_intl(selector, options = {}) {
        const config = {
            phone: true,
            phoneRegionCode: 'FR',
            ...options
        };

        return this.create(selector, config);
    },

    /** Masque personnalisé par blocs (alias x_clmask_blocks) */
    blocks(selector, blocksArray, delimiter = ' ', options = {}) {
        const config = {
            blocks: blocksArray,
            delimiter: delimiter,
            ...options
        };

        return this.create(selector, config);
    },

    /** Masque numérique simple (alias x_clmask_numeric) */
    numeric(selector, options = {}) {
        const config = {
            numericOnly: true,
            ...options
        };

        return this.create(selector, config);
    },

    /** Masque alphabétique (alias x_clmask_alpha) */
    alpha(selector, options = {}) {
        const config = {
            blocks: [50], // Longueur maximale
            delimiter: '',
            numericOnly: false,
            ...options
        };

        return this.create(selector, config);
    },

    /** Masque alphanumérique (alias x_clmask_alphanum) */
    alphanum(selector, options = {}) {
        const config = {
            blocks: [50], // Longueur maximale
            delimiter: '',
            ...options
        };

        return this.create(selector, config);
    },

    // ======================== UTILITAIRES ========================

    /** Obtenir la valeur brute (alias x_clmask_getRawValue) */
    getRawValue(selector) {
        const instance = this.getInstance(selector);
        return instance ? instance.getRawValue() : '';
    },

    /** Obtenir la valeur formatée (alias x_clmask_getFormattedValue) */
    getFormattedValue(selector) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        return element ? element.value : '';
    },

    /** Définir la valeur (alias x_clmask_setValue) */
    setValue(selector, value) {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.setRawValue(value);
        }
        return this;
    },

    /** Vider la valeur (alias x_clmask_clear) */
    clear(selector) {
        return this.setValue(selector, '');
    },

    /** Activer/désactiver (alias x_clmask_toggle) */
    toggle(selector, enabled = true) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (element) {
            element.disabled = !enabled;
        }
        return this;
    },

    /** Statistiques (alias x_clmask_getStats) */
    getStats() {
        return {
            totalInstances: this.instances.size,
            activeInstances: Array.from(this.instances.values()).filter(i => i && !i.destroyed).length
        };
    },

    /** Détruire toutes les instances (alias x_clmask_destroyAll) */
    destroyAll() {
        this.instances.forEach((instance, id) => {
            if (instance && !instance.destroyed) {
                instance.destroy();
            }
        });
        this.instances.clear();
        return this;
    }
};

// ======================== ALIAS ========================
const x_clmask_create = X_CLMASK.create.bind(X_CLMASK);
const x_clmask_destroy = X_CLMASK.destroy.bind(X_CLMASK);
const x_clmask_getInstance = X_CLMASK.getInstance.bind(X_CLMASK);
const x_clmask_phone = X_CLMASK.phone.bind(X_CLMASK);
const x_clmask_date = X_CLMASK.date.bind(X_CLMASK);
const x_clmask_number = X_CLMASK.number.bind(X_CLMASK);
const x_clmask_currency = X_CLMASK.currency.bind(X_CLMASK);
const x_clmask_percent = X_CLMASK.percent.bind(X_CLMASK);
const x_clmask_creditcard = X_CLMASK.creditcard.bind(X_CLMASK);
const x_clmask_iban = X_CLMASK.iban.bind(X_CLMASK);
const x_clmask_postal = X_CLMASK.postal.bind(X_CLMASK);
const x_clmask_siret = X_CLMASK.siret.bind(X_CLMASK);
const x_clmask_nir = X_CLMASK.nir.bind(X_CLMASK);
const x_clmask_time = X_CLMASK.time.bind(X_CLMASK);
const x_clmask_phone_intl = X_CLMASK.phone_intl.bind(X_CLMASK);
const x_clmask_blocks = X_CLMASK.blocks.bind(X_CLMASK);
const x_clmask_numeric = X_CLMASK.numeric.bind(X_CLMASK);
const x_clmask_alpha = X_CLMASK.alpha.bind(X_CLMASK);
const x_clmask_alphanum = X_CLMASK.alphanum.bind(X_CLMASK);
const x_clmask_getRawValue = X_CLMASK.getRawValue.bind(X_CLMASK);
const x_clmask_getFormattedValue = X_CLMASK.getFormattedValue.bind(X_CLMASK);
const x_clmask_setValue = X_CLMASK.setValue.bind(X_CLMASK);
const x_clmask_clear = X_CLMASK.clear.bind(X_CLMASK);
const x_clmask_toggle = X_CLMASK.toggle.bind(X_CLMASK);
const x_clmask_getStats = X_CLMASK.getStats.bind(X_CLMASK);
const x_clmask_destroyAll = X_CLMASK.destroyAll.bind(X_CLMASK);

// Export pour le navigateur
if (typeof window !== 'undefined') {
    window.X_CLMASK = X_CLMASK;
}

// Export pour les modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = X_CLMASK;
}

/* 
EXEMPLES D'UTILISATION :

// ============ MASQUES BASIQUES ============

// Téléphone français
x_clmask_phone('#phone', 'fr');

// Date
x_clmask_date('#birthday', 'dd/mm/yyyy');

// Numérique avec séparateurs
x_clmask_number('#amount');

// Monétaire
x_clmask_currency('#price', 'EUR');

// Pourcentage
x_clmask_percent('#rate');

// ============ MASQUES SPÉCIALISÉS ============

// Carte de crédit
x_clmask_creditcard('#card');

// IBAN
x_clmask_iban('#iban');

// Code postal
x_clmask_postal('#postal', 'fr');

// SIRET
x_clmask_siret('#siret', 'siret');

// NIR (Sécurité Sociale)
x_clmask_nir('#nir');

// Heure
x_clmask_time('#time', '24h');

// ============ MASQUES PERSONNALISÉS ============

// Par blocs
x_clmask_blocks('#code', [3, 3, 4], '-');

// Numérique seulement
x_clmask_numeric('#quantity');

// Alphabétique seulement
x_clmask_alpha('#name');

// Configuration avancée
x_clmask_create('#custom', {
    blocks: [4, 4, 4, 4],
    delimiter: '-',
    uppercase: true
});

// ============ GESTION DES VALEURS ============

// Définir une valeur
x_clmask_setValue('#phone', '0123456789');

// Obtenir la valeur brute
const raw = x_clmask_getRawValue('#phone');

// Obtenir la valeur formatée
const formatted = x_clmask_getFormattedValue('#phone');

// Vider un champ
x_clmask_clear('#phone');

// ============ CONTRÔLE ============

// Activer/désactiver
x_clmask_toggle('#phone', false); // désactiver
x_clmask_toggle('#phone', true);  // activer

// Détruire une instance
x_clmask_destroy('#phone');

// Détruire toutes les instances
x_clmask_destroyAll();

// Statistiques
const stats = x_clmask_getStats();
console.log('Instances actives:', stats.activeInstances);

*/

// ============ CDN REQUIS ============
/*
Pour utiliser cette bibliothèque, incluez d'abord Cleave.js :

<!-- Cleave.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>

<!-- Extensions Cleave.js (optionnelles) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/addons/cleave-phone.fr.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/addons/cleave-phone.us.js"></script>

<!-- Puis incluez X_CLMASK -->
<script src="path/to/x_clmask.js"></script>
*/