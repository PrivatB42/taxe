/**
 * Bibliothèque X_DATEPICKER - Wrapper pour Flatpickr
 * @version 1.0
 * @license MIT
 * @requires Flatpickr (https://flatpickr.js.org/)
 */
const X_DATEPICKER = {
    // Configuration par défaut
    defaults: {
        dateFormat: "Y-m-d",
        altFormat: "Y-m-d",
        altInput: true,
        locale: "fr",
        allowInput: true,
        clickOpens: true
    },

    // Instance courante
    currentInstance: null,

    // ======================== INITIALISATION ========================

    /** Créer un datepicker sur un élément (alias x_datePicker_create) */
    create(selector, options = {}) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (!element) {
            throw new Error('Élément non trouvé');
        }

        const config = { ...this.defaults, ...options };
        this.currentInstance = flatpickr(element, config);
        return this.currentInstance;
    },

    /** Créer un datepicker simple (alias x_datePicker_simple) */
    simple(selector, format = "Y-m-d") {
        return this.create(selector, {
            dateFormat: format,
            altFormat: format
        });
    },

    /** Créer un datepicker avec heure (alias x_datePicker_datetime) */
    datetime(selector, format = "Y-m-d H:i") {
        return this.create(selector, {
            enableTime: true,
            dateFormat: format,
            altFormat: format,
            time_24hr: true
        });
    },

    /** Créer un sélecteur de temps uniquement (alias x_datePicker_time) */
    time(selector, format = "H:i") {
        return this.create(selector, {
            enableTime: true,
            noCalendar: true,
            dateFormat: format,
            altFormat: format,
            time_24hr: true
        });
    },

    /** Créer un sélecteur de plage de dates (alias x_datePicker_range) */
    range(selector, options = {}) {
        return this.create(selector, {
            mode: "range",
            dateFormat: "Y-m-d",
            altFormat: "Y-m-d",
            ...options
        });
    },

    /** Créer un sélecteur multiple (alias x_datePicker_multiple) */
    multiple(selector, options = {}) {
        return this.create(selector, {
            mode: "multiple",
            dateFormat: "Y-m-d",
            altFormat: "Y-m-d",
            ...options
        });
    },

    // ======================== CONFIGURATION ========================

    /** Définir la langue (alias x_datePicker_locale) */
    locale(selector, lang = "fr") {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.set("locale", lang);
        }
        return this;
    },

    /** Définir le format de date (alias x_datePicker_format) */
    format(selector, format = "Y-m-d") {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.set("dateFormat", format);
            instance.set("altFormat", format);
        }
        return this;
    },

    /** Activer/désactiver l'heure (alias x_datePicker_enableTime) */
    enableTime(selector, enable = true, format24h = true) {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.set("enableTime", enable);
            instance.set("time_24hr", format24h);
        }
        return this;
    },

    /** Définir les dates min et max (alias x_datePicker_minMax) */
    minMax(selector, minDate = null, maxDate = null) {
        const instance = this.getInstance(selector);
        if (instance) {
            if (minDate) instance.set("minDate", minDate);
            if (maxDate) instance.set("maxDate", maxDate);
        }
        return this;
    },

    /** Définir la date par défaut (alias x_datePicker_defaultDate) */
    defaultDate(selector, date) {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.set("defaultDate", date);
        }
        return this;
    },

    /** Désactiver des dates spécifiques (alias x_datePicker_disable) */
    disable(selector, dates) {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.set("disable", dates);
        }
        return this;
    },

    /** Activer seulement des dates spécifiques (alias x_datePicker_enable) */
    enable(selector, dates) {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.set("enable", dates);
        }
        return this;
    },

    /** Désactiver les weekends (alias x_datePicker_noWeekends) */
    noWeekends(selector) {
        return this.disable(selector, [
            function(date) {
                return (date.getDay() === 0 || date.getDay() === 6);
            }
        ]);
    },

    /** Désactiver les jours passés (alias x_datePicker_noPassedDays) */
    noPassedDays(selector) {
        return this.minMax(selector, "today");
    },

    // ======================== INTERACTION ========================

    /** Ouvrir le datepicker (alias x_datePicker_open) */
    open(selector) {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.open();
        }
        return this;
    },

    /** Fermer le datepicker (alias x_datePicker_close) */
    close(selector) {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.close();
        }
        return this;
    },

    /** Basculer l'état du datepicker (alias x_datePicker_toggle) */
    toggle(selector) {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.toggle();
        }
        return this;
    },

    /** Vider la sélection (alias x_datePicker_clear) */
    clear(selector) {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.clear();
        }
        return this;
    },

    /** Détruire l'instance (alias x_datePicker_destroy) */
    destroy(selector) {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.destroy();
        }
        return this;
    },

    // ======================== VALEURS ========================

    /** Obtenir la date sélectionnée (alias x_datePicker_getValue) */
    getValue(selector, formatted = false) {
        const instance = this.getInstance(selector);
        if (!instance) return null;
        
        return formatted ? instance.input.value : instance.selectedDates;
    },

    /** Définir une date (alias x_datePicker_setValue) */
    setValue(selector, date) {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.setDate(date);
        }
        return this;
    },

    /** Obtenir la date au format texte (alias x_datePicker_getFormatted) */
    getFormatted(selector, format = null) {
        const instance = this.getInstance(selector);
        if (!instance || !instance.selectedDates.length) return '';
        
        if (format) {
            return instance.formatDate(instance.selectedDates[0], format);
        }
        return instance.input.value;
    },

    /** Obtenir le timestamp (alias x_datePicker_getTimestamp) */
    getTimestamp(selector) {
        const instance = this.getInstance(selector);
        if (!instance || !instance.selectedDates.length) return null;
        
        return instance.selectedDates[0].getTime();
    },

    /** Vérifier si une date est sélectionnée (alias x_datePicker_hasValue) */
    hasValue(selector) {
        const instance = this.getInstance(selector);
        return instance && instance.selectedDates.length > 0;
    },

    // ======================== ÉVÉNEMENTS ========================

    /** Ajouter un écouteur d'événement (alias x_datePicker_on) */
    on(selector, event, callback) {
        const instance = this.getInstance(selector);
        if (instance) {
            instance.config[`on${event.charAt(0).toUpperCase()}${event.slice(1)}`] = callback;
        }
        return this;
    },

    /** Événement onChange (alias x_datePicker_onChange) */
    onChange(selector, callback) {
        return this.on(selector, 'change', callback);
    },

    /** Événement onOpen (alias x_datePicker_onOpen) */
    onOpen(selector, callback) {
        return this.on(selector, 'open', callback);
    },

    /** Événement onClose (alias x_datePicker_onClose) */
    onClose(selector, callback) {
        return this.on(selector, 'close', callback);
    },

    /** Événement onReady (alias x_datePicker_onReady) */
    onReady(selector, callback) {
        return this.on(selector, 'ready', callback);
    },

    // ======================== VALIDATION ========================

    /** Valider si c'est une date valide (alias x_datePicker_isValid) */
    isValid(selector) {
        const instance = this.getInstance(selector);
        if (!instance) return false;
        
        return instance.selectedDates.length > 0;
    },

    /** Valider si la date est dans la plage (alias x_datePicker_isInRange) */
    isInRange(selector, minDate = null, maxDate = null) {
        const instance = this.getInstance(selector);
        if (!instance || !instance.selectedDates.length) return false;
        
        const selectedDate = instance.selectedDates[0];
        
        if (minDate && selectedDate < new Date(minDate)) return false;
        if (maxDate && selectedDate > new Date(maxDate)) return false;
        
        return true;
    },

    /** Valider si c'est un jour de semaine (alias x_datePicker_isWeekday) */
    isWeekday(selector) {
        const instance = this.getInstance(selector);
        if (!instance || !instance.selectedDates.length) return false;
        
        const day = instance.selectedDates[0].getDay();
        return day >= 1 && day <= 5;
    },

    // ======================== UTILITAIRES ========================

    /** Obtenir l'instance Flatpickr (alias x_datePicker_getInstance) */
    getInstance(selector) {
        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        return element && element._flatpickr ? element._flatpickr : this.currentInstance;
    },

    /** Créer un preset rapide (alias x_datePicker_preset) */
    preset(selector, type, options = {}) {
        const presets = {
            birthday: {
                maxDate: "today",
                defaultDate: new Date(1990, 0, 1),
                dateFormat: "Y-m-d"
            },
            appointment: {
                minDate: "today",
                enableTime: true,
                time_24hr: true,
                dateFormat: "Y-m-d H:i"
            },
            vacation: {
                mode: "range",
                minDate: "today",
                dateFormat: "Y-m-d"
            },
            deadline: {
                minDate: "today",
                dateFormat: "Y-m-d",
                disable: [
                    function(date) {
                        return (date.getDay() === 0 || date.getDay() === 6);
                    }
                ]
            }
        };

        const config = { ...presets[type], ...options };
        return this.create(selector, config);
    },

    /** Convertir en différents formats (alias x_datePicker_convert) */
    convert(selector, targetFormat) {
        const instance = this.getInstance(selector);
        if (!instance || !instance.selectedDates.length) return '';
        
        return instance.formatDate(instance.selectedDates[0], targetFormat);
    },

    /** Ajouter des jours à la date sélectionnée (alias x_datePicker_addDays) */
    addDays(selector, days) {
        const instance = this.getInstance(selector);
        if (instance && instance.selectedDates.length) {
            const newDate = new Date(instance.selectedDates[0]);
            newDate.setDate(newDate.getDate() + days);
            instance.setDate(newDate);
        }
        return this;
    },

    /** Obtenir la différence en jours (alias x_datePicker_diffDays) */
    diffDays(selector, compareDate = new Date()) {
        const instance = this.getInstance(selector);
        if (!instance || !instance.selectedDates.length) return null;
        
        const selectedDate = instance.selectedDates[0];
        const timeDiff = selectedDate.getTime() - new Date(compareDate).getTime();
        return Math.ceil(timeDiff / (1000 * 3600 * 24));
    },

    /** Thèmes prédéfinis (alias x_datePicker_theme) */
    theme(selector, themeName = 'default') {
        const themes = {
            default: {},
            dark: {
                theme: 'dark'
            },
            material: {
                theme: 'material_blue'
            },
            airbnb: {
                theme: 'airbnb'
            }
        };

        const element = typeof selector === 'string' ? document.querySelector(selector) : selector;
        if (element && themes[themeName]) {
            // Ajouter les classes CSS nécessaires
            const themeClass = `flatpickr-${themeName}`;
            element.classList.add(themeClass);
        }
        return this;
    }
};

// ======================== ALIAS ========================
const x_datePicker_create = X_DATEPICKER.create.bind(X_DATEPICKER);
const x_datePicker_simple = X_DATEPICKER.simple.bind(X_DATEPICKER);
const x_datePicker_datetime = X_DATEPICKER.datetime.bind(X_DATEPICKER);
const x_datePicker_time = X_DATEPICKER.time.bind(X_DATEPICKER);
const x_datePicker_range = X_DATEPICKER.range.bind(X_DATEPICKER);
const x_datePicker_multiple = X_DATEPICKER.multiple.bind(X_DATEPICKER);
const x_datePicker_locale = X_DATEPICKER.locale.bind(X_DATEPICKER);
const x_datePicker_format = X_DATEPICKER.format.bind(X_DATEPICKER);
const x_datePicker_enableTime = X_DATEPICKER.enableTime.bind(X_DATEPICKER);
const x_datePicker_minMax = X_DATEPICKER.minMax.bind(X_DATEPICKER);
const x_datePicker_defaultDate = X_DATEPICKER.defaultDate.bind(X_DATEPICKER);
const x_datePicker_disable = X_DATEPICKER.disable.bind(X_DATEPICKER);
const x_datePicker_enable = X_DATEPICKER.enable.bind(X_DATEPICKER);
const x_datePicker_noWeekends = X_DATEPICKER.noWeekends.bind(X_DATEPICKER);
const x_datePicker_noPassedDays = X_DATEPICKER.noPassedDays.bind(X_DATEPICKER);
const x_datePicker_open = X_DATEPICKER.open.bind(X_DATEPICKER);
const x_datePicker_close = X_DATEPICKER.close.bind(X_DATEPICKER);
const x_datePicker_toggle = X_DATEPICKER.toggle.bind(X_DATEPICKER);
const x_datePicker_clear = X_DATEPICKER.clear.bind(X_DATEPICKER);
const x_datePicker_destroy = X_DATEPICKER.destroy.bind(X_DATEPICKER);
const x_datePicker_getValue = X_DATEPICKER.getValue.bind(X_DATEPICKER);
const x_datePicker_setValue = X_DATEPICKER.setValue.bind(X_DATEPICKER);
const x_datePicker_getFormatted = X_DATEPICKER.getFormatted.bind(X_DATEPICKER);
const x_datePicker_getTimestamp = X_DATEPICKER.getTimestamp.bind(X_DATEPICKER);
const x_datePicker_hasValue = X_DATEPICKER.hasValue.bind(X_DATEPICKER);
const x_datePicker_on = X_DATEPICKER.on.bind(X_DATEPICKER);
const x_datePicker_onChange = X_DATEPICKER.onChange.bind(X_DATEPICKER);
const x_datePicker_onOpen = X_DATEPICKER.onOpen.bind(X_DATEPICKER);
const x_datePicker_onClose = X_DATEPICKER.onClose.bind(X_DATEPICKER);
const x_datePicker_onReady = X_DATEPICKER.onReady.bind(X_DATEPICKER);
const x_datePicker_isValid = X_DATEPICKER.isValid.bind(X_DATEPICKER);
const x_datePicker_isInRange = X_DATEPICKER.isInRange.bind(X_DATEPICKER);
const x_datePicker_isWeekday = X_DATEPICKER.isWeekday.bind(X_DATEPICKER);
const x_datePicker_getInstance = X_DATEPICKER.getInstance.bind(X_DATEPICKER);
const x_datePicker_preset = X_DATEPICKER.preset.bind(X_DATEPICKER);
const x_datePicker_convert = X_DATEPICKER.convert.bind(X_DATEPICKER);
const x_datePicker_addDays = X_DATEPICKER.addDays.bind(X_DATEPICKER);
const x_datePicker_diffDays = X_DATEPICKER.diffDays.bind(X_DATEPICKER);
const x_datePicker_theme = X_DATEPICKER.theme.bind(X_DATEPICKER);

// Export pour le navigateur
if (typeof window !== 'undefined') {
    window.X_DATEPICKER = X_DATEPICKER;
}

// Export pour les modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = X_DATEPICKER;
}

/* 
EXEMPLES D'UTILISATION :

// Créer un datepicker simple
x_datePicker_simple('#birthday');

// Créer un datepicker avec heure
x_datePicker_datetime('#appointment', 'Y-m-d H:i');

// Créer un sélecteur de plage
x_datePicker_range('#vacation');

// Désactiver les weekends
x_datePicker_noWeekends('#workday');

// Utiliser un preset
x_datePicker_preset('#appointment', 'appointment');

// Chaîner les méthodes
x_datePicker_create('#mydate')
    .locale('fr')
    .format('Y-m-d')
    .noWeekends()
    .onChange((selectedDates) => {
        console.log('Date sélectionnée:', selectedDates);
    });

// Validation
if (x_datePicker_isValid('#mydate')) {
    const formatted = x_datePicker_getFormatted('#mydate');
    console.log('Date:', formatted);
}

// Manipulation de dates
x_datePicker_setValue('#mydate', 'today');
x_datePicker_addDays('#mydate', 7); // Ajouter 7 jours

// Obtenir la différence
const days = x_datePicker_diffDays('#mydate'); // Différence avec aujourd'hui



<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr" defer></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js" defer></script>

*/