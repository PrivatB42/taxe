/**
 * Bibliothèque X_DOM - Manipulation avancée du DOM
 * @version 3.0
 * @license MIT
 */
const X_DOM = {
    // ======================== SÉLECTION ========================
    /** Sélection par ID (alias x_) */
    _(id, callback = null) {
        const el = document.getElementById(id);
        if (!el) console.warn(`⚠️ Élément #${id} introuvable`);
        callback?.(el);
        return el;
    },

    /** Sélection par classe (alias x_class) */
    class(className, callback = null) {
        const els = document.getElementsByClassName(className);
        callback?.(els);
        return els;
    },

    /** Sélection CSS avancée (alias x_selectQuery) */
    query(selector, callback = null) {
        const els = document.querySelectorAll(selector);
        if (els.length === 0)
            console.warn(`⚠️ Sélecteur "${selector}" introuvable`);
        const result = els.length === 1 ? els[0] : els;
        callback?.(result);
        return result;
    },

    /** Sélection relative (alias x_you_selectQuery) */
    xquery(element, selector, callback = null) {
        const el = this._getElement(element);
        const els = el.querySelectorAll(selector);
        callback?.(els);
        return els;
    },

    /** NOUVEAU: Sélection avec fallback (alias x_safe) */
    safe(selector, fallback = null, callback = null) {
        const el =
            typeof selector === "string"
                ? selector.startsWith("#")
                    ? this._(selector.slice(1))
                    : this.query(selector)
                : this._getElement(selector);
        const result = el || fallback;
        callback?.(result);
        return result;
    },



    /** NOUVEAU: Sélection avec retry (alias x_waitFor) */
    async waitFor(selector, timeout = 5000, interval = 100) {
        return new Promise((resolve, reject) => {
            const startTime = Date.now();
            const check = () => {
                const el = this.query(selector);
                if (el && (el.length ? el.length > 0 : true)) {
                    resolve(el);
                } else if (Date.now() - startTime >= timeout) {
                    reject(new Error(`Timeout: ${selector} non trouvé`));
                } else {
                    setTimeout(check, interval);
                }
            };
            check();
        });
    },

    // ======================== CONTENU ========================
    /** Modifier texte (alias x_textContent) */
    text(idOrEl, value = undefined, callback = null) {
        const el = this._getElement(idOrEl);
        if (value === undefined) return el?.textContent;
        if (el) el.textContent = value;
        callback?.(el);
        return el;
    },

    /** Modifier multiples textes (alias x_textContents) */
    texts(elements = []) {
        return elements.map((item) =>
            this.text(item.id, item.value, item?.callback)
        );
    },

    /** Modifier HTML (alias x_inner) */
    html(idOrEl, value = undefined, callback = null) {
        const el = this._getElement(idOrEl);
        if (value === undefined) return el?.innerHTML;
        if (el) el.innerHTML = value;
        callback?.(el);
        return el;
    },

    /** Modifier multiples HTML (alias x_inners) */
    htmls(elements = []) {
        return elements.map((item) =>
            this.html(item.id, item.value, item?.callback)
        );
    },

    /** Modifier valeur (alias x_val) */
    val(idOrEl, value = undefined, callback = null) {
        const el = this._getElement(idOrEl);
        if (value === undefined) return el?.value;
        if (el) el.value = value;
        callback?.(el);
        return el;
    },

    /** Get/Set multiples valeurs (alias x_vals) */
    vals(elements, values = undefined) {
        if (values !== undefined || !Array.isArray(elements)) {
            const data = Array.isArray(elements) ? values : elements;
            Object.entries(data).forEach(([id, val]) => this.val(id, val));
            return data;
        }
        const result = {};
        elements.forEach((id) => {
            result[id] = this.val(id);
        });
        return result;
    },

    /** NOUVEAU: Gestion du contenu avec template (alias x_template) */
    template(idOrEl, template, data = {}, callback = null) {
        const el = this._getElement(idOrEl);
        if (!el) return;

        const compiled = template.replace(/\{\{(\w+)\}\}/g, (match, key) => {
            return data[key] !== undefined ? data[key] : match;
        });

        el.innerHTML = compiled;
        callback?.(el, data);
        return el;
    },

    // ======================== CRÉATION ========================
    /** Créer élément (alias x_create) */
    create(tag, config = {}, callback = null) {
        const el = document.createElement(tag);
        Object.entries(config).forEach(([key, val]) => {
            if (key === "textContent" || key === "innerHTML") {
                el[key] = val;
            } else if (key === "style" && typeof val === "object") {
                Object.assign(el.style, val);
            } else if (key === "dataset" && typeof val === "object") {
                Object.entries(val).forEach(([k, v]) => (el.dataset[k] = v));
            } else {
                el[key] = val;
            }
        });
        callback?.(el);
        return el;
    },

    /** Créer multiples éléments (alias x_multi_create) */
    createMultiple(items = []) {
        return items.map((item) =>
            this.create(item.tag, item.attrs, item?.callback)
        );
    },

    /** NOUVEAU: Fragment de document (alias x_fragment) */
    fragment(elements = []) {
        const fragment = document.createDocumentFragment();
        elements.forEach((el) => {
            const element =
                typeof el === "string"
                    ? this.create("div", { innerHTML: el })
                    : el;
            fragment.appendChild(element);
        });
        return fragment;
    },

    // ======================== DOM ========================
    /** Ajouter éléments (alias x_append) */
    append(parent, children, callback = null) {
        const parentEl = this._getElement(parent);
        if (!parentEl) return;
        const childrenArr = Array.isArray(children) ? children : [children];
        callback?.(parentEl, childrenArr);
        childrenArr.forEach((child) => parentEl.appendChild(child));
    },

    /** Prépendre éléments (alias x_prepend) */
    prepend(parent, children, callback = null) {
        const parentEl = this._getElement(parent);
        if (!parentEl) return;
        const childrenArr = Array.isArray(children) ? children : [children];
        callback?.(parentEl, childrenArr);
        childrenArr.forEach((child) =>
            parentEl.insertBefore(child, parentEl.firstChild)
        );
    },

    /** Supprimer éléments (alias x_remove) */
    remove(parent, children) {
        const parentEl = this._getElement(parent);
        if (!parentEl) return;
        const childrenArr = Array.isArray(children) ? children : [children];
        childrenArr.forEach((child) => parentEl.removeChild(child));
    },

    /** NOUVEAU: Vider complètement (alias x_empty) */
    empty(idOrEl, callback = null) {
        const el = this._getElement(idOrEl);
        if (el) {
            el.innerHTML = "";
            callback?.(el);
        }
        return el;
    },

    /** NOUVEAU: Remplacer élément (alias x_replace) */
    replace(oldEl, newEl, callback = null) {
        const old = this._getElement(oldEl);
        const newElement = this._getElement(newEl);
        if (old && newElement) {
            old.parentNode.replaceChild(newElement, old);
            callback?.(newElement, old);
        }
        return newElement;
    },

    // ======================== CLASSES/CSS ========================
    /** Manipuler les classes (alias x_classAction) */
    classAction(idOrEl, classes, action = "add", callback = null) {
        const el = this._getElement(idOrEl);
        if (!el) return;
        const classList = Array.isArray(classes) ? classes : [classes];
        classList.forEach((cls) => el.classList[action](cls));
        callback?.(el);
        return el;
    },

    /** NOUVEAU: Toggle classes conditionnel (alias x_toggleClass) */
    toggleClass(idOrEl, className, condition = null, callback = null) {
        const el = this._getElement(idOrEl);
        if (!el) return;

        if (condition !== null) {
            el.classList.toggle(className, condition);
        } else {
            el.classList.toggle(className);
        }

        callback?.(el, el.classList.contains(className));
        return el;
    },

    /** Modifier les styles (alias x_css) */
    css(idOrEl, style, value = null, callback = null) {
        const el = this._getElement(idOrEl);
        if (!el) return;
        if (typeof style === "object") {
            Object.assign(el.style, style);
        } else {
            el.style[style] = value;
        }
        callback?.(el);
        return el;
    },

    /** NOUVEAU: Variables CSS (alias x_cssVar) */
    cssVar(idOrEl, varName, value = undefined) {
        const el = this._getElement(idOrEl) || document.documentElement;
        if (value === undefined) {
            return getComputedStyle(el).getPropertyValue(`--${varName}`);
        }
        el.style.setProperty(`--${varName}`, value);
        return el;
    },

    // ======================== ATTRIBUTS ========================
    /** Manipuler les attributs (alias x_attr) */
    attr(idOrEl, attr, value = null, callback = null) {
        const el = this._getElement(idOrEl);
        if (!el) return;
        if (typeof attr === "object") {
            Object.entries(attr).forEach(([k, v]) => el.setAttribute(k, v));
        } else if (value === null && typeof attr === "string") {
            return el.getAttribute(attr);
        } else {
            el.setAttribute(attr, value);
        }
        callback?.(el);
        return el;
    },

    /** NOUVEAU: Supprimer attributs (alias x_removeAttr) */
    removeAttr(idOrEl, attrs, callback = null) {
        const el = this._getElement(idOrEl);
        if (!el) return;
        const attrList = Array.isArray(attrs) ? attrs : [attrs];
        attrList.forEach((attr) => el.removeAttribute(attr));
        callback?.(el);
        return el;
    },

    // ======================== ANIMATIONS ========================
    /** Animation simple (alias x_animate) */
    animate(idOrEl, animation, onComplete = null) {
        const el = this._getElement(idOrEl);
        if (!el) return;
        if (typeof animation === "string") {
            el.style.animation = animation;
            el.addEventListener("animationend", () => onComplete?.(), {
                once: true,
            });
        } else {
            // Implémentation JS custom
        }
        return el;
    },

    /** NOUVEAU: Transitions fluides (alias x_transition) */
    transition(
        idOrEl,
        properties,
        duration = 300,
        easing = "ease",
        callback = null
    ) {
        const el = this._getElement(idOrEl);
        if (!el) return;

        const originalTransition = el.style.transition;
        el.style.transition = `all ${duration}ms ${easing}`;

        if (typeof properties === "object") {
            Object.assign(el.style, properties);
        }

        setTimeout(() => {
            el.style.transition = originalTransition;
            callback?.(el);
        }, duration);

        return el;
    },

    /** NOUVEAU: Effets visuels (alias x_effect) */
    effect(idOrEl, effectName, options = {}) {
        const el = this._getElement(idOrEl);
        if (!el) return;

        const effects = {
            fadeIn: () =>
                this.transition(el, { opacity: 1 }, options.duration || 300),
            fadeOut: () =>
                this.transition(el, { opacity: 0 }, options.duration || 300),
            slideDown: () => {
                el.style.height = "0px";
                el.style.overflow = "hidden";
                el.style.display = "block";
                this.transition(
                    el,
                    { height: el.scrollHeight + "px" },
                    options.duration || 300,
                    "ease",
                    () => {
                        el.style.height = "auto";
                    }
                );
            },
            slideUp: () => {
                el.style.height = el.scrollHeight + "px";
                this.transition(
                    el,
                    { height: "0px" },
                    options.duration || 300,
                    "ease",
                    () => {
                        el.style.display = "none";
                    }
                );
            },
            shake: () => {
                el.style.animation = "shake 0.5s ease-in-out";
                setTimeout(() => (el.style.animation = ""), 500);
            },
            pulse: () => {
                el.style.animation = "pulse 1s infinite";
                setTimeout(
                    () => (el.style.animation = ""),
                    options.duration || 2000
                );
            },
        };

        effects[effectName]?.();
        return el;
    },

    // ======================== Verifications ========================

    verifierString(str, type, search = "") {
        switch (type) {
            case "numeric":
                return /\d/.test(str); // au moins un chiffre

            case "alpha":
                return /[a-zA-Z]/.test(str); // au moins une lettre

            case "alphanumeric":
                return /[a-zA-Z0-9]/.test(str); // au moins lettre OU chiffre

            case "contains":
                return str.includes(search); // contient une sous-chaîne précise

            default:
                throw new Error("Type de test non supporté : " + type);
        }
    },

    // ======================== ÉVÉNEMENTS ========================
    /** Gestionnaire d'événement (alias x_event) */
    on(target, event, handler, options = {}) {
        const el = this._getElement(target);
        el?.addEventListener(event, (e) => handler.call(e.target, e), options);
        return () => el?.removeEventListener(event, handler, options);
    },

    /** Multi-événements (alias x_multi_event) */
    onMultiple(events = []) {
        const cleanups = events.map((e) =>
            this.on(e.target, e.event, e.handler, e.options)
        );
        return () => cleanups.forEach((fn) => fn());
    },

    /** NOUVEAU: Événement délégué (alias x_delegate) */
    delegate(parentSelector, childSelector, event, handler) {
        const parent = this._getElement(parentSelector);
        if (!parent) return;

        const delegatedHandler = (e) => {
            if (e.target.matches(childSelector)) {
                handler.call(e.target, e);
            }
        };

        parent.addEventListener(event, delegatedHandler);
        return () => parent.removeEventListener(event, delegatedHandler);
    },

    /** NOUVEAU: Événement une fois (alias x_once) */
    once(target, event, handler) {
        return this.on(target, event, handler, { once: true });
    },

    /** Debounce (alias x_debounce) */
    debounce(fn, delay = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    },

    /** NOUVEAU: Throttle (alias x_throttle) */
    throttle(fn, delay = 300) {
        let lastCall = 0;
        return (...args) => {
            const now = Date.now();
            if (now - lastCall >= delay) {
                lastCall = now;
                fn.apply(this, args);
            }
        };
    },

    // ======================== REQUÊTES ========================
    /** Fetch simplifié (alias x_fetch) */
    async fetch(url, options = {}, data = null, callbacks = null) {
        const defaultOptions = { method: "GET" };
        if (data) {
            defaultOptions.headers = { "Content-Type": "application/json" };
            defaultOptions.body = JSON.stringify(data);
            defaultOptions.method = "POST";
        }
        
        options = { ...defaultOptions, ...options };

        try {
            callbacks?.loading?.(true);
            const response = await fetch(url, options);

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || "Erreur serveur");
            }

            const contentType = response.headers.get("content-type");
            let result;

            if (contentType?.includes("application/json")) {
                result = await response.json();
            } else if (contentType?.includes("text/")) {
                result = await response.text();
            } else if (contentType?.includes("file/")) {
                result = await response.blob();
            } else {
                result = response;
            }

            callbacks?.success?.(result, response);
            return result;
        } catch (e) {
            console.error("❌ Erreur fetch", e);
            callbacks?.error?.(e);
            return null;
        } finally {
            callbacks?.loading?.(false);
        }
    },

    /** NOUVEAU: Requêtes en parallèle (alias x_fetchAll) */
    async fetchAll(requests = []) {
        try {
            const promises = requests.map((req) =>
                this.fetch(req.url, req.options, req.data, req.callbacks)
            );
            return await Promise.allSettled(promises);
        } catch (e) {
            console.error("❌ Erreur fetchAll", e);
            return [];
        }
    },

    // ======================== STOCKAGE ========================
    /** Gestion storage (alias x_storage) */
    storage(key, value = undefined, type = "local") {
        const storage = type === "local" ? localStorage : sessionStorage;
        if (value === undefined) {
            const data = storage.getItem(key);
            try {
                return JSON.parse(data);
            } catch {
                return data;
            }
        }
        storage.setItem(
            key,
            typeof value === "string" ? value : JSON.stringify(value)
        );
        return value;
    },

    /** NOUVEAU: Storage avec expiration (alias x_cache) */
    cache(key, value = undefined, ttl = 3600000) {
        // 1h par défaut
        const cacheKey = `xdom_cache_${key}`;
        const timeKey = `xdom_time_${key}`;

        if (value === undefined) {
            const data = this.storage(cacheKey);
            const timestamp = this.storage(timeKey);

            if (data && timestamp && Date.now() - timestamp < ttl) {
                return data;
            }

            // Nettoyer si expiré
            this.storage(cacheKey, null);
            this.storage(timeKey, null);
            return null;
        }

        this.storage(cacheKey, value);
        this.storage(timeKey, Date.now());
        return value;
    },

    // ======================== OBSERVATION ========================
    /** Observer le DOM (alias x_observe) */
    observe(idOrEl, callback, options = { childList: true }) {
        const el = this._getElement(idOrEl);
        if (!el) return;
        const observer = new MutationObserver(callback);
        observer.observe(el, options);
        return () => observer.disconnect();
    },

    /** NOUVEAU: Observer l'intersection (alias x_intersect) */
    intersect(idOrEl, callback, options = {}) {
        const el = this._getElement(idOrEl);
        if (!el) return;

        const defaultOptions = { threshold: 0.1, ...options };
        const observer = new IntersectionObserver(callback, defaultOptions);
        observer.observe(el);

        return () => observer.disconnect();
    },

    /** NOUVEAU: Observer le redimensionnement (alias x_resize) */
    resize(idOrEl, callback) {
        const el = this._getElement(idOrEl);
        if (!el) return;

        const observer = new ResizeObserver(callback);
        observer.observe(el);

        return () => observer.disconnect();
    },

    // ========================== Formatage =====================

    /**
     * Formate un nombre ou une chaîne en montant avec devise
     * @param {number|string} amount - Montant à formater (ex: 1000, "1000.50")
     * @param {string} currency - Code devise (ex: "EUR", "USD", "XOF")
     * @param {string} locale - Locale (ex: "fr-FR", "en-US")
     * @returns {string} - Montant formaté (ex: "1 000 €", "$1,000.50")
     */
    number_format(
        amount,
        currency = "EUR",
        locale = "fr-FR",
        options = { min: 0, max: 2, mode: "currency" }
    ) {
        const number =
            typeof amount === "string"
                ? parseFloat(amount.replace(/\s/g, ""))
                : amount;

        const config = {
            minimumFractionDigits: options.min,
            maximumFractionDigits: options.max,
        }

        if (options.mode === "currency") {
            config.style = "currency";
            config.currency = currency;
        }

        return new Intl.NumberFormat(locale, config).format(number);
    },

    // ======================== DRAG & DROP ========================
    /** Drag & Drop (alias x_draggable) */
    draggable(idOrEl, options = {}, callback = null) {
        const el = this._getElement(idOrEl);
        if (!el) return;
        el.draggable = true;
        el.addEventListener("dragstart", (e) => {
            e.dataTransfer.setData("text/plain", options.data || el.id);
            callback?.(e);
        });
        return el;
    },

    /** NOUVEAU: Zone de drop (alias x_dropzone) */
    dropzone(idOrEl, onDrop, options = {}) {
        const el = this._getElement(idOrEl);
        if (!el) return;

        const handlers = {
            dragover: (e) => {
                e.preventDefault();
                el.classList.add(options.hoverClass || "drag-hover");
            },
            dragleave: (e) => {
                e.preventDefault();
                el.classList.remove(options.hoverClass || "drag-hover");
            },
            drop: (e) => {
                e.preventDefault();
                el.classList.remove(options.hoverClass || "drag-hover");

                const data = e.dataTransfer.getData("text/plain");
                const files = Array.from(e.dataTransfer.files);

                onDrop({ data, files, event: e });
            },
        };

        Object.entries(handlers).forEach(([event, handler]) => {
            el.addEventListener(event, handler);
        });

        return () => {
            Object.entries(handlers).forEach(([event, handler]) => {
                el.removeEventListener(event, handler);
            });
        };
    },

    // ======================== FORMULAIRES ========================
    /** FormData simplifié (alias x_formData) */
    formData(idOrEl) {
        const el = this._getElement(idOrEl);
        if (!el || el.tagName !== "FORM") return;
        const data = new FormData(el);
        return Object.fromEntries(data.entries());
    },

    /** NOUVEAU: Validation de formulaire (alias x_validate) */
    validate(formId, rules = {}, callback = null) {
        const form = this._(formId);
        if (!form) return false;

        const errors = {};
        const data = this.formData(form);

        Object.entries(rules).forEach(([field, rule]) => {
            const value = data[field];

            if (rule.required && (!value || value.trim() === "")) {
                errors[field] =
                    rule.messages?.required || "Ce champ est requis";
            } else if (value && rule.pattern && !rule.pattern.test(value)) {
                errors[field] = rule.messages?.pattern || "Format invalide";
            } else if (value && rule.min && value.length < rule.min) {
                errors[field] =
                    rule.messages?.min || `Minimum ${rule.min} caractères`;
            } else if (value && rule.max && value.length > rule.max) {
                errors[field] =
                    rule.messages?.max || `Maximum ${rule.max} caractères`;
            }
        });

        const isValid = Object.keys(errors).length === 0;
        callback?.({ isValid, errors, data });

        return { isValid, errors, data };
    },

    // ======================== UTILITAIRES ========================
    /** Parser JSON (alias x_jParse) */
    parse(data) {
        try {
            return JSON.parse(data);
        } catch (e) {
            console.error("❌ Erreur JSON.parse", e);
            return [];
        }
    },

    /** Stringify JSON (alias x_jStringify) */
    stringify(data, pretty = false) {
        return pretty ? JSON.stringify(data, null, 2) : JSON.stringify(data);
    },

    /** Traitement multiple (alias x_multi) */
    batch(elements = [], callback = null) {
        return elements.map((el) => {
            const element = this._getElement(el);
            callback?.(element);
            return element;
        });
    },

    /** NOUVEAU: Utilitaires diverses (alias x_utils) */
    utils: {
        /** Générer un ID unique */
        uid: (prefix = "xdom") =>
            `${prefix}_${Date.now()}_${Math.random()
                .toString(36)
                .substr(2, 9)}`,

        /** Délai asynchrone */
        sleep: (ms) => new Promise((resolve) => setTimeout(resolve, ms)),

        /** Vérifier si élément est visible */
        isVisible: (el) => {
            const element = X_DOM._getElement(el);
            if (!element) return false;
            const rect = element.getBoundingClientRect();
            return (
                rect.width > 0 &&
                rect.height > 0 &&
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= window.innerHeight &&
                rect.right <= window.innerWidth
            );
        },

        /** Obtenir les dimensions */
        dimensions: (el) => {
            const element = X_DOM._getElement(el);
            if (!element) return null;
            const rect = element.getBoundingClientRect();
            return {
                width: rect.width,
                height: rect.height,
                top: rect.top,
                left: rect.left,
                right: rect.right,
                bottom: rect.bottom,
            };
        },

        /** Scroll vers élément */
        scrollTo: (el, options = {}) => {
            const element = X_DOM._getElement(el);
            if (!element) return;
            element.scrollIntoView({
                behavior: "smooth",
                block: "center",
                ...options,
            });
        },
    },

    /** NOUVEAU: État des éléments (alias x_state) */
    state(idOrEl, stateName = undefined, value = undefined) {
        const el = this._getElement(idOrEl);
        if (!el) return;

        if (!el._xdomState) el._xdomState = {};

        if (stateName === undefined) {
            return el._xdomState;
        }

        if (value === undefined) {
            return el._xdomState[stateName];
        }

        el._xdomState[stateName] = value;
        return el;
    },

    // ======================== COMPOSANTS ========================
    /** NOUVEAU: Créer un composant réutilisable (alias x_component) */
    component(name, definition) {
        this._components = this._components || {};

        if (typeof definition === "function") {
            this._components[name] = definition;
        }

        return (container, props = {}) => {
            const containerEl = this._getElement(container);
            if (!containerEl || !this._components[name]) return;

            return this._components[name](containerEl, props, this);
        };
    },

    /** NOUVEAU: Toast notifications (alias x_toast) */
    toast(message, type = "info", duration = 3000) {
        const toastContainer =
            this._("xdom-toast-container") || this._createToastContainer();

        const toast = this.create("div", {
            className: `xdom-toast xdom-toast-${type}`,
            innerHTML: `
                <span class="xdom-toast-message">${message}</span>
                <button class="xdom-toast-close">×</button>
            `,
        });

        this.append(toastContainer, toast);

        // Animation d'entrée
        setTimeout(() => this.classAction(toast, "show", "add"), 10);

        // Fermeture automatique
        const autoClose = setTimeout(() => this._removeToast(toast), duration);

        // Fermeture manuelle
        this.on(toast.querySelector(".xdom-toast-close"), "click", () => {
            clearTimeout(autoClose);
            this._removeToast(toast);
        });

        return toast;
    },

    /** NOUVEAU: Loader/Spinner (alias x_loader) */
    loader(idOrEl, show = true, options = {}) {
        const el = this._getElement(idOrEl);
        if (!el) return;

        const loaderId = `${el.id || "element"}-loader`;
        let loader = this._(loaderId);

        if (show && !loader) {
            loader = this.create("div", {
                id: loaderId,
                className: `xdom-loader ${options.class || ""}`,
                innerHTML:
                    options.html ||
                    `
                    <div class="xdom-spinner"></div>
                    <span>${options.text || "Chargement..."}</span>
                `,
            });

            if (options.overlay) {
                this.css(loader, {
                    position: "absolute",
                    top: "0",
                    left: "0",
                    width: "100%",
                    height: "100%",
                    background: "rgba(255, 255, 255, 0.8)",
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    zIndex: "1000",
                });

                this.css(el, { position: "relative" });
            }

            this.append(el, loader);
            this.effect(loader, "fadeIn", { duration: 200 });
        } else if (!show && loader) {
            this.effect(loader, "fadeOut", { duration: 200 });
            setTimeout(() => loader.remove(), 200);
        }

        return loader;
    },

    // ======================== NAVIGATION ========================
    /** Initialisation Datatable (alias x_datatable) */
    datatable(tableId) {
        if (window.DataTableUtils && window.DataTableUtils[tableId]) {
            return window.DataTableUtils[tableId];
        }
        return false;
    },

    /** Reloader (alias x_reload) */
    reload(duration = null) {
        if (duration) {
            setTimeout(() => window.location.reload(), duration);
        } else {
            window.location.reload();
        }
    },

    /**
     * Navigation par onglets/accordéon
     * @param {string} containerId - ID du conteneur
     * @param {Object} [options] - Configuration
     */
    initTabs(containerId, options = {}) {
        const defaults = {
            tabSelector: ".tab-header",
            contentSelector: ".tab-content",
            activeClass: "active",
            event: "click",
        };
        const config = { ...defaults, ...options };
        const container = this._(containerId);
        if (!container) return;

        const tabs = container.querySelectorAll(config.tabSelector);
        const contents = container.querySelectorAll(config.contentSelector);

        tabs.forEach((tab, index) => {
            this.on(tab, config.event, () => {
                // Désactiver tout
                tabs.forEach((t) => t.classList.remove(config.activeClass));
                contents.forEach((c) => c.classList.remove(config.activeClass));

                // Activer l'onglet courant
                tab.classList.add(config.activeClass);
                contents[index]?.classList.add(config.activeClass);

                config.onTabChange?.(index, tab, contents[index]);
            });
        });

        return { tabs, contents };
    },

    // ======================== MANIPULATION AVANCÉE ========================
    /**
     * Créer un élément avec des enfants imbriqués
     * @param {Object} config - Configuration de l'élément
     * @example { tag: 'div', attrs: { class: 'parent' }, children: [
     *   { tag: 'span', text: 'Enfant 1' },
     *   { tag: 'span', text: 'Enfant 2' }
     * ]}
     */
    createNested(config) {
        const element = this.create(config.tag, config.attrs || {});

        if (config.text) element.textContent = config.text;
        if (config.html) element.innerHTML = config.html;

        if (config.children) {
            config.children.forEach((childConfig) => {
                const child = this.createNested(childConfig);
                this.append(element, child);
            });
        }

        return element;
    },

    /**
     * Transformer un tableau en éléments DOM
     * @param {Array} data - Données à transformer
     * @param {Function} template - Fonction de template
     */
    renderList(containerId, data, template, callback = null) {
        const container = this._(containerId);
        if (!container) return;

        const fragments = data.map((item, index) => {
            const element = template(item, index);
            element.dataset.index = index;
            return element;
        });

        container.innerHTML = "";
        this.append(container, fragments);
        callback?.(fragments);
        return fragments;
    },

    /** NOUVEAU: Table dynamique (alias x_table) */
    table(containerId, data, columns, options = {}) {
        const container = this._(containerId);
        if (!container) return;

        const table = this.create("table", {
            className: options.tableClass || "xdom-table",
        });

        // En-têtes
        const thead = this.create("thead");
        const headerRow = this.create("tr");

        columns.forEach((col) => {
            const th = this.create("th", {
                textContent: col.label || col.key,
                className: col.headerClass || "",
            });
            this.append(headerRow, th);
        });

        this.append(thead, headerRow);
        this.append(table, thead);

        // Corps du tableau
        const tbody = this.create("tbody");

        data.forEach((row, rowIndex) => {
            const tr = this.create("tr", {
                dataset: { index: rowIndex },
            });

            columns.forEach((col) => {
                const td = this.create("td", {
                    className: col.cellClass || "",
                });

                const value = row[col.key];
                if (col.render && typeof col.render === "function") {
                    td.innerHTML = col.render(value, row, rowIndex);
                } else {
                    td.textContent = value || "";
                }

                this.append(tr, td);
            });

            this.append(tbody, tr);
        });

        this.append(table, tbody);
        container.innerHTML = "";
        this.append(container, table);

        return table;
    },

    // ======================== FORMULAIRES DYNAMIQUES ========================
    /**
     * Multiplier un élément de formulaire (ex: champs dynamiques)
     * @param {string} templateId - ID du template à cloner
     * @param {string} containerId - ID du conteneur parent
     * @param {number} count - Nombre de copies à créer
     * @param {Function} [config] - Configuration des clones
     */
    multiply(templateId, containerId, count, config = null) {
        const template = this._(templateId);
        const container = this._(containerId);
        if (!template || !container) return;

        // Supprimer les clones existants (optionnel)
        container
            .querySelectorAll(`[data-clone-of="${templateId}"]`)
            .forEach((el) => el.remove());

        // Créer les clones
        const fragments = [];
        for (let i = 0; i < count; i++) {
            const clone = template.cloneNode(true);
            clone.removeAttribute("id");
            clone.dataset.cloneOf = templateId;
            clone.dataset.index = i;

            // Configurer les clones
            if (config) {
                config(clone, i);
            }

            fragments.push(clone);
        }

        this.append(container, fragments);
        return fragments;
    },

    /**
     * Générer des inputs dynamiquement avec gestion des noms
     * @param {string} containerId - ID du conteneur
     * @param {Array} fields - Configuration des champs
     * @param {string} [baseName] - Préfixe pour les attributs 'name'
     */
    generateForm(containerId, fields, baseName = "dynamic") {
        const container = this._(containerId);
        if (!container) return;

        container.innerHTML = "";
        fields.forEach((field, index) => {
            const fieldId = `${baseName}-${index}`;
            const fieldName = `${baseName}[${index}]`;

            const wrapper = this.create("div", { className: "form-field" });
            const label = this.create("label", {
                textContent: field.label || `Field ${index + 1}`,
                htmlFor: fieldId,
            });

            let input;
            switch (field.type) {
                case "textarea":
                    input = this.create("textarea", {
                        id: fieldId,
                        name: fieldName,
                        className: "form-control",
                    });
                    break;
                case "select":
                    input = this.create("select", {
                        id: fieldId,
                        name: fieldName,
                        className: "form-control",
                    });
                    field.options?.forEach((opt) => {
                        this.append(
                            input,
                            this.create("option", {
                                value: opt.value,
                                textContent: opt.label,
                            })
                        );
                    });
                    break;
                default:
                    input = this.create("input", {
                        type: field.type || "text",
                        id: fieldId,
                        name: fieldName,
                        className: "form-control",
                        placeholder: field.placeholder || "",
                    });
            }

            this.append(wrapper, [label, input]);
            this.append(container, wrapper);
        });
    },

    // ======================== UTILITAIRES AVANCÉS ========================
    /**
     * Détecter les clics en dehors d'un élément
     * @param {string} elementId - ID de l'élément à surveiller
     * @param {Function} callback - Fonction à exécuter
     */
    onClickOutside(elementId, callback) {
        const element = this._(elementId);
        if (!element) return;

        const handler = (e) => {
            if (!element.contains(e.target)) {
                callback(e);
            }
        };

        document.addEventListener("click", handler);
        return () => document.removeEventListener("click", handler);
    },

    /**
     * Copier du texte dans le clipboard
     * @param {string} text - Texte à copier
     * @param {Function} [onSuccess] - Callback de succès
     */
    copyToClipboard(text, onSuccess = null, onError = null) {
        if (navigator.clipboard) {
            navigator.clipboard
                .writeText(text)
                .then(() => onSuccess?.())
                .catch((err) => {
                    console.error("Erreur de copie:", err);
                    onError?.(err);
                });
        } else {
            // Fallback pour navigateurs anciens
            const textarea = this.create("textarea", { value: text });
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand("copy");
                onSuccess?.();
            } catch (err) {
                onError?.(err);
            }
            document.body.removeChild(textarea);
        }
    },

    /**
     * Gestionnaire de modales
     * @param {string} modalId - ID de la modale
     * @param {Object} [options] - Options
     */
    // modal(modalId, options = {}) {
    //     const modal = this._(modalId);
    //     if (!modal) return;

    //     const defaults = {
    //         openSelector: `[data-open="${modalId}"]`,
    //         closeSelector: `[data-close="${modalId}"]`,
    //         backdrop: true,
    //         keyboard: true,
    //         animation: true
    //     };
    //     const config = { ...defaults, ...options };

    //     // Ouvrir la modale
    //     document.querySelectorAll(config.openSelector).forEach((btn) => {
    //         this.on(btn, "click", () => {
    //             this.classAction(modal, "open", "add");
    //             if (config.animation) {
    //                 this.effect(modal, 'fadeIn', { duration: 200 });
    //             }
    //             config.onOpen?.(modal);
    //         });
    //     });

    //     // Fermer la modale
    //     document.querySelectorAll(config.closeSelector).forEach((btn) => {
    //         this.on(btn, "click", () => this._closeModal(modal, config));
    //     });

    //     // Fermer en cliquant à l'extérieur
    //     if (config.backdrop) {
    //         this.on(modal, 'click', (e) => {
    //             if (e.target === modal) {
    //                 this._closeModal(modal, config);
    //             }
    //         });
    //     }

    //     // Fermer avec Escape
    //     if (config.keyboard) {
    //         this.on(document, 'keydown', (e) => {
    //             if (e.key === 'Escape' && modal.classList.contains('open')) {
    //                 this._closeModal(modal, config);
    //             }
    //         });
    //     }

    //     return modal;
    // },

    /**
     * Gestionnaire de modales
     * @param {string} modalId - ID de la modale
     * @param {Object} [options] - Options
     */
    modal(modalId, options = {}) {
        const modal = this._(modalId);
        if (!modal) return;

        const defaults = {
            openSelector: `[data-open="${modalId}"]`,
            closeSelector: `[data-close="${modalId}"]`,
            backdrop: true,
            keyboard: true,
            animation: true,
        };
        const config = { ...defaults, ...options };

        // Ouvrir la modale
        document.querySelectorAll(config.openSelector).forEach((btn) => {
            this.on(btn, "click", () => {
                this.classAction(modal, "open", "add");
                if (config.animation) {
                    this.effect(modal, "fadeIn", { duration: 200 });
                }
                config.onOpen?.(modal);
            });
        });

        // Fermer la modale
        document.querySelectorAll(config.closeSelector).forEach((btn) => {
            this.on(btn, "click", () => this._closeModal(modal, config));
        });

        // Fermer en cliquant à l'extérieur
        if (config.backdrop) {
            this.on(modal, "click", (e) => {
                if (e.target === modal) {
                    this._closeModal(modal, config);
                }
            });
        }

        // Fermer avec Escape
        if (config.keyboard) {
            this.on(document, "keydown", (e) => {
                if (e.key === "Escape" && modal.classList.contains("open")) {
                    this._closeModal(modal, config);
                }
            });
        }

        return modal;
    },

    /** NOUVEAU: Gestionnaire de progression (alias x_progress) */
    progress(idOrEl, value = undefined, options = {}) {
        const el = this._getElement(idOrEl);
        if (!el) return;

        if (value === undefined) {
            return parseFloat(el.getAttribute("aria-valuenow") || 0);
        }

        const percentage = Math.min(100, Math.max(0, value));

        // Mise à jour visuelle
        const bar = el.querySelector(".progress-bar") || el;
        this.css(bar, { width: `${percentage}%` });

        // Attributs d'accessibilité
        this.attr(el, {
            "aria-valuenow": percentage,
            "aria-valuemin": options.min || 0,
            "aria-valuemax": options.max || 100,
        });

        // Texte optionnel
        if (options.showText) {
            const text =
                el.querySelector(".progress-text") ||
                this.create("span", { className: "progress-text" });
            text.textContent = `${Math.round(percentage)}%`;
            if (!text.parentNode) this.append(el, text);
        }

        options.onChange?.(percentage, el);
        return el;
    },

    // ======================== RECHARGEMENT ========================
    /**
     * Recharge un élément du DOM
     * @param {string|HTMLElement} element - ID ou élément
     * @param {Object} [options] - Configuration
     */
    reloadElement: function (element, options = {}) {
        const el = this._getElement(element);
        if (!el) return console.error(`Élément ${element} introuvable`);

        const config = {
            method: "innerHTML",
            animation: "fadeIn 0.3s",
            preserveEvents: false,
            ...options,
        };

        config.before?.(el);

        if (config.url) {
            return this._fetchReload(el, config);
        }

        this._applyReload(el, el[config.method], config);
        config.after?.(el);
        return el;
    },

    /**
     * Re-exécute une fonction avec options avancées
     * @param {Function} fn - Fonction à exécuter
     * @param {Object} [options] - Configuration
     */
    reloadFunction: function (fn, options = {}) {
        if (typeof fn !== "function") {
            throw new Error("Le paramètre doit être une fonction");
        }

        const config = {
            args: [],
            retries: 0,
            delay: 0,
            cache: false,
            ...options,
        };

        return this._executeWithRetry(fn, config);
    },

    // ======================== UTILISATEUR ========================
    /**
     * Gestion utilisateur (localStorage)
     * @param {Object} [data] - Données utilisateur
     */
    user: function (data = undefined) {
        const key = "xdom_user";
        if (data === undefined) {
            return this.storage(key) || {};
        }
        this.storage(key, data);
        return data;
    },

    // ======================== NOUVEAUX MODULES ========================

    /** NOUVEAU: Gestionnaire de médias (alias x_media) */
    media: {
        /** Charger une image avec callback */
        loadImage: function (src, callback = null) {
            const img = new Image();
            img.onload = () => callback?.(img, true);
            img.onerror = () => callback?.(img, false);
            img.src = src;
            return img;
        },

        /** Précharger plusieurs images */
        preloadImages: function (urls, onProgress = null, onComplete = null) {
            let loaded = 0;
            const total = urls.length;
            const images = [];

            urls.forEach((url, index) => {
                this.loadImage(url, (img, success) => {
                    images[index] = { img, success, url };
                    loaded++;
                    onProgress?.(loaded, total, img, success);

                    if (loaded === total) {
                        onComplete?.(images);
                    }
                });
            });

            return images;
        },

        /** Redimensionner image canvas */
        resizeImage: function (file, maxWidth, maxHeight, quality = 0.8) {
            return new Promise((resolve) => {
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");
                const img = new Image();

                img.onload = () => {
                    const ratio = Math.min(
                        maxWidth / img.width,
                        maxHeight / img.height
                    );
                    canvas.width = img.width * ratio;
                    canvas.height = img.height * ratio;

                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    canvas.toBlob(resolve, "image/jpeg", quality);
                };

                img.src = URL.createObjectURL(file);
            });
        },
    },

    /** NOUVEAU: Gestionnaire de cookies (alias x_cookies) */
    cookies: {
        set: function (name, value, days = 7, path = "/") {
            const expires = new Date();
            expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
            document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=${path}`;
        },

        get: function (name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(";");
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === " ") c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0)
                    return c.substring(nameEQ.length, c.length);
            }
            return null;
        },

        remove: function (name, path = "/") {
            document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=${path}`;
        },

        exists: function (name) {
            return this.get(name) !== null;
        },
    },

    /** NOUVEAU: Gestionnaire de thèmes (alias x_theme) */
    theme: {
        set: function (themeName, persist = true) {
            document.documentElement.setAttribute("data-theme", themeName);
            if (persist) {
                X_DOM.storage("xdom_theme", themeName);
            }
        },

        get: function () {
            return (
                document.documentElement.getAttribute("data-theme") ||
                X_DOM.storage("xdom_theme") ||
                "default"
            );
        },

        toggle: function (themes = ["light", "dark"]) {
            const current = this.get();
            const currentIndex = themes.indexOf(current);
            const nextIndex = (currentIndex + 1) % themes.length;
            this.set(themes[nextIndex]);
            return themes[nextIndex];
        },

        auto: function () {
            const prefersDark = window.matchMedia(
                "(prefers-color-scheme: dark)"
            ).matches;
            this.set(prefersDark ? "dark" : "light");

            // Écouter les changements
            window
                .matchMedia("(prefers-color-scheme: dark)")
                .addEventListener("change", (e) => {
                    this.set(e.matches ? "dark" : "light");
                });
        },
    },

    /** NOUVEAU: Performance et optimisation (alias x_perf) */
    perf: {
        /** Mesurer le temps d'exécution */
        time: function (fn, label = "X_DOM_Timer") {
            console.time(label);
            const result = fn();
            console.timeEnd(label);
            return result;
        },

        /** Lazy loading d'images */
        lazyImages: function (selector = "img[data-src]", options = {}) {
            const images = document.querySelectorAll(selector);
            const config = { rootMargin: "50px", ...options };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove("lazy");
                        img.classList.add("loaded");
                        observer.unobserve(img);
                    }
                });
            }, config);

            images.forEach((img) => observer.observe(img));
            return () => observer.disconnect();
        },

        /** Optimiser les animations */
        requestFrame: function (callback) {
            return requestAnimationFrame(callback);
        },

        /** Batch des opérations DOM */
        batchDOM: function (operations) {
            const fragment = document.createDocumentFragment();
            operations.forEach((op) => op(fragment));
            return fragment;
        },
    },

    /** NOUVEAU: Accessibilité (alias x_a11y) */
    a11y: {
        /** Focus management */
        focus: function (idOrEl, options = {}) {
            const el = X_DOM._getElement(idOrEl);
            if (!el) return;

            if (options.preventScroll) {
                el.focus({ preventScroll: true });
            } else {
                el.focus();
            }

            if (options.outline === false) {
                el.style.outline = "none";
                setTimeout(() => (el.style.outline = ""), 100);
            }
        },

        /** Gestion des rôles ARIA */
        role: function (idOrEl, role, properties = {}) {
            const el = X_DOM._getElement(idOrEl);
            if (!el) return;

            el.setAttribute("role", role);
            Object.entries(properties).forEach(([key, value]) => {
                el.setAttribute(`aria-${key}`, value);
            });

            return el;
        },

        /** Navigation au clavier */
        keyboard: function (container, items, options = {}) {
            const containerEl = X_DOM._getElement(container);
            if (!containerEl) return;

            const config = {
                selector:
                    options.selector ||
                    "[tabindex], button, input, select, textarea, a[href]",
                loop: options.loop !== false,
                ...options,
            };

            let currentIndex = 0;
            const elements = Array.from(
                containerEl.querySelectorAll(config.selector)
            );

            const handler = (e) => {
                switch (e.key) {
                    case "ArrowDown":
                    case "ArrowRight":
                        e.preventDefault();
                        currentIndex = config.loop
                            ? (currentIndex + 1) % elements.length
                            : Math.min(currentIndex + 1, elements.length - 1);
                        elements[currentIndex]?.focus();
                        break;
                    case "ArrowUp":
                    case "ArrowLeft":
                        e.preventDefault();
                        currentIndex = config.loop
                            ? (currentIndex - 1 + elements.length) %
                              elements.length
                            : Math.max(currentIndex - 1, 0);
                        elements[currentIndex]?.focus();
                        break;
                    case "Home":
                        e.preventDefault();
                        currentIndex = 0;
                        elements[currentIndex]?.focus();
                        break;
                    case "End":
                        e.preventDefault();
                        currentIndex = elements.length - 1;
                        elements[currentIndex]?.focus();
                        break;
                }
            };

            containerEl.addEventListener("keydown", handler);
            return () => containerEl.removeEventListener("keydown", handler);
        },
    },

    /** NOUVEAU: Workers et tâches lourdes (alias x_worker) */
    worker: {
        /** Créer un worker inline */
        create: function (fn, onMessage = null, onError = null) {
            const blob = new Blob([`(${fn.toString()})()`], {
                type: "application/javascript",
            });
            const worker = new Worker(URL.createObjectURL(blob));

            if (onMessage) worker.onmessage = onMessage;
            if (onError) worker.onerror = onError;

            return worker;
        },

        /** Exécuter une tâche lourde sans bloquer l'UI */
        offload: function (fn, data, callback = null) {
            const workerFn = function () {
                self.onmessage = function (e) {
                    try {
                        const result = fn.toString()(e.data);
                        self.postMessage({ success: true, result });
                    } catch (error) {
                        self.postMessage({
                            success: false,
                            error: error.message,
                        });
                    }
                };
            };

            const worker = this.create(workerFn, (e) => {
                const { success, result, error } = e.data;
                if (success) {
                    callback?.(null, result);
                } else {
                    callback?.(new Error(error), null);
                }
                worker.terminate();
            });

            worker.postMessage(data);
            return worker;
        },
    },

    // ======================== NOUVELLES FONCTIONNALITÉS X_DOM v3.0 ========================

    // Ajoutez ces méthodes à votre objet X_DOM existant :

    // ======================== CANVAS UTILITIES ========================
    /** NOUVEAU: Gestionnaire Canvas (alias x_canvas) */
    canvas: {
        /** Créer un canvas avec contexte */
        create: function (width, height, container = null) {
            const canvas = X_DOM.create("canvas", {
                width: width,
                height: height,
                style: { border: "1px solid #ddd" },
            });

            const ctx = canvas.getContext("2d");

            if (container) {
                const containerEl = X_DOM._getElement(container);
                if (containerEl) X_DOM.append(containerEl, canvas);
            }

            return { canvas, ctx };
        },

        /** Dessiner des formes de base */
        draw: {
            rect: function (
                ctx,
                x,
                y,
                width,
                height,
                fill = true,
                color = "#000"
            ) {
                ctx.fillStyle = color;
                ctx.strokeStyle = color;
                if (fill) {
                    ctx.fillRect(x, y, width, height);
                } else {
                    ctx.strokeRect(x, y, width, height);
                }
            },

            circle: function (ctx, x, y, radius, fill = true, color = "#000") {
                ctx.beginPath();
                ctx.arc(x, y, radius, 0, 2 * Math.PI);
                ctx.fillStyle = color;
                ctx.strokeStyle = color;
                if (fill) {
                    ctx.fill();
                } else {
                    ctx.stroke();
                }
            },

            line: function (ctx, x1, y1, x2, y2, color = "#000", width = 1) {
                ctx.beginPath();
                ctx.moveTo(x1, y1);
                ctx.lineTo(x2, y2);
                ctx.strokeStyle = color;
                ctx.lineWidth = width;
                ctx.stroke();
            },

            text: function (ctx, text, x, y, options = {}) {
                const config = {
                    font: "16px Arial",
                    color: "#000",
                    align: "left",
                    baseline: "top",
                    ...options,
                };

                ctx.font = config.font;
                ctx.fillStyle = config.color;
                ctx.textAlign = config.align;
                ctx.textBaseline = config.baseline;
                ctx.fillText(text, x, y);
            },

            path: function (
                ctx,
                points,
                closed = false,
                color = "#000",
                width = 1
            ) {
                if (points.length < 2) return;

                ctx.beginPath();
                ctx.moveTo(points[0].x, points[0].y);

                for (let i = 1; i < points.length; i++) {
                    ctx.lineTo(points[i].x, points[i].y);
                }

                if (closed) ctx.closePath();

                ctx.strokeStyle = color;
                ctx.lineWidth = width;
                ctx.stroke();
            },
        },

        /** Outils de canvas */
        utils: {
            clear: function (ctx, x = 0, y = 0, width = null, height = null) {
                width = width || ctx.canvas.width;
                height = height || ctx.canvas.height;
                ctx.clearRect(x, y, width, height);
            },

            save: function (canvas, filename = "canvas.png") {
                const link = X_DOM.create("a", {
                    download: filename,
                    href: canvas.toDataURL(),
                });
                link.click();
            },

            resize: function (canvas, width, height, scale = true) {
                if (scale) {
                    const tempCanvas = document.createElement("canvas");
                    const tempCtx = tempCanvas.getContext("2d");
                    tempCanvas.width = canvas.width;
                    tempCanvas.height = canvas.height;
                    tempCtx.drawImage(canvas, 0, 0);

                    canvas.width = width;
                    canvas.height = height;
                    canvas
                        .getContext("2d")
                        .drawImage(tempCanvas, 0, 0, width, height);
                } else {
                    canvas.width = width;
                    canvas.height = height;
                }
            },

            toBlob: function (
                canvas,
                callback,
                type = "image/png",
                quality = 0.92
            ) {
                canvas.toBlob(callback, type, quality);
            },

            getImageData: function (
                ctx,
                x = 0,
                y = 0,
                width = null,
                height = null
            ) {
                width = width || ctx.canvas.width;
                height = height || ctx.canvas.height;
                return ctx.getImageData(x, y, width, height);
            },

            putImageData: function (ctx, imageData, x = 0, y = 0) {
                ctx.putImageData(imageData, x, y);
            },
        },

        /** Gestionnaire de dessin interactif */
        drawing: {
            enable: function (canvasId, options = {}) {
                const canvas = X_DOM._(canvasId);
                if (!canvas) return;

                const ctx = canvas.getContext("2d");
                const config = {
                    color: "#000",
                    size: 2,
                    tool: "pen", // pen, eraser, line, rect, circle
                    ...options,
                };

                let isDrawing = false;
                let startPos = {};
                let currentPath = [];

                const handlers = {
                    mousedown: (e) => {
                        isDrawing = true;
                        const rect = canvas.getBoundingClientRect();
                        startPos = {
                            x: e.clientX - rect.left,
                            y: e.clientY - rect.top,
                        };
                        currentPath = [startPos];
                    },

                    mousemove: (e) => {
                        if (!isDrawing) return;
                        const rect = canvas.getBoundingClientRect();
                        const pos = {
                            x: e.clientX - rect.left,
                            y: e.clientY - rect.top,
                        };

                        switch (config.tool) {
                            case "pen":
                                X_DOM.canvas.draw.line(
                                    ctx,
                                    currentPath[currentPath.length - 1].x,
                                    currentPath[currentPath.length - 1].y,
                                    pos.x,
                                    pos.y,
                                    config.color,
                                    config.size
                                );
                                break;
                            case "eraser":
                                ctx.globalCompositeOperation =
                                    "destination-out";
                                X_DOM.canvas.draw.circle(
                                    ctx,
                                    pos.x,
                                    pos.y,
                                    config.size,
                                    true
                                );
                                ctx.globalCompositeOperation = "source-over";
                                break;
                        }
                        currentPath.push(pos);
                    },

                    mouseup: () => {
                        isDrawing = false;
                        currentPath = [];
                    },
                };

                Object.entries(handlers).forEach(([event, handler]) => {
                    canvas.addEventListener(event, handler);
                });

                return () => {
                    Object.entries(handlers).forEach(([event, handler]) => {
                        canvas.removeEventListener(event, handler);
                    });
                };
            },
        },
    },

    /** NOUVEAU: Gestionnaire de géolocalisation (alias x_geo) */
    geo: {
        /** Obtenir position actuelle */
        getCurrentPosition: function (onSuccess, onError = null, options = {}) {
            const config = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000,
                ...options,
            };

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    onSuccess,
                    onError,
                    config
                );
            } else {
                onError?.(new Error("Géolocalisation non supportée"));
            }
        },

        /** Surveiller position */
        watchPosition: function (onSuccess, onError = null, options = {}) {
            if (navigator.geolocation) {
                return navigator.geolocation.watchPosition(
                    onSuccess,
                    onError,
                    options
                );
            }
            return null;
        },

        /** Calculer distance entre deux points */
        distance: function (lat1, lon1, lat2, lon2) {
            const R = 6371; // Rayon de la Terre en km
            const dLat = ((lat2 - lat1) * Math.PI) / 180;
            const dLon = ((lon2 - lon1) * Math.PI) / 180;
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos((lat1 * Math.PI) / 180) *
                    Math.cos((lat2 * Math.PI) / 180) *
                    Math.sin(dLon / 2) *
                    Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        },
    },

    /** NOUVEAU: Audio et son (alias x_audio) */
    audio: {
        /** Créer un lecteur audio */
        create: function (src, options = {}) {
            const audio = X_DOM.create("audio", {
                src: src,
                controls: options.controls !== false,
                autoplay: options.autoplay || false,
                loop: options.loop || false,
                volume: options.volume || 1,
            });

            return {
                element: audio,
                play: () => audio.play(),
                pause: () => audio.pause(),
                stop: () => {
                    audio.pause();
                    audio.currentTime = 0;
                },
                volume: (vol) => {
                    if (vol !== undefined) audio.volume = vol;
                    return audio.volume;
                },
                currentTime: (time) => {
                    if (time !== undefined) audio.currentTime = time;
                    return audio.currentTime;
                },
                duration: () => audio.duration,
            };
        },

        /** Notifications sonores */
        notify: function (type = "success") {
            const frequencies = {
                success: [523, 659, 784],
                error: [392, 311, 247],
                warning: [440, 554, 659],
                info: [523, 659],
            };

            if (window.AudioContext || window.webkitAudioContext) {
                const audioCtx = new (window.AudioContext ||
                    window.webkitAudioContext)();
                const notes = frequencies[type] || frequencies.info;

                notes.forEach((freq, index) => {
                    const oscillator = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioCtx.destination);

                    oscillator.frequency.value = freq;
                    oscillator.type = "sine";

                    gainNode.gain.setValueAtTime(
                        0.1,
                        audioCtx.currentTime + index * 0.1
                    );
                    gainNode.gain.exponentialRampToValueAtTime(
                        0.01,
                        audioCtx.currentTime + index * 0.1 + 0.1
                    );

                    oscillator.start(audioCtx.currentTime + index * 0.1);
                    oscillator.stop(audioCtx.currentTime + index * 0.1 + 0.1);
                });
            }
        },
    },

    /** NOUVEAU: Date et temps (alias x_date) */
    date: {
        /** Formater une date */
        format: function (date, format = "dd/mm/yyyy") {
            const d = new Date(date);
            const tokens = {
                yyyy: d.getFullYear(),
                mm: String(d.getMonth() + 1).padStart(2, "0"),
                dd: String(d.getDate()).padStart(2, "0"),
                hh: String(d.getHours()).padStart(2, "0"),
                ii: String(d.getMinutes()).padStart(2, "0"),
                ss: String(d.getSeconds()).padStart(2, "0"),
            };

            return format.replace(
                /yyyy|mm|dd|hh|ii|ss/g,
                (match) => tokens[match]
            );
        },

        /** Différence entre dates */
        diff: function (date1, date2, unit = "days") {
            const d1 = new Date(date1);
            const d2 = new Date(date2);
            const diffMs = Math.abs(d2 - d1);

            const units = {
                seconds: 1000,
                minutes: 1000 * 60,
                hours: 1000 * 60 * 60,
                days: 1000 * 60 * 60 * 24,
                weeks: 1000 * 60 * 60 * 24 * 7,
                months: 1000 * 60 * 60 * 24 * 30,
                years: 1000 * 60 * 60 * 24 * 365,
            };

            return Math.floor(diffMs / (units[unit] || units.days));
        },

        /** Date relative (il y a X temps) */
        relative: function (date) {
            const now = new Date();
            const target = new Date(date);
            const diffMs = now - target;

            if (diffMs < 60000) return "À l'instant";
            if (diffMs < 3600000)
                return `Il y a ${Math.floor(diffMs / 60000)} min`;
            if (diffMs < 86400000)
                return `Il y a ${Math.floor(diffMs / 3600000)}h`;
            if (diffMs < 604800000)
                return `Il y a ${Math.floor(diffMs / 86400000)} jours`;

            return this.format(target);
        },

        /** Countdown timer */
        countdown: function (targetDate, callback, interval = 1000) {
            const timer = setInterval(() => {
                const now = new Date().getTime();
                const target = new Date(targetDate).getTime();
                const distance = target - now;

                if (distance < 0) {
                    clearInterval(timer);
                    callback({
                        expired: true,
                        days: 0,
                        hours: 0,
                        minutes: 0,
                        seconds: 0,
                    });
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor(
                    (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
                );
                const minutes = Math.floor(
                    (distance % (1000 * 60 * 60)) / (1000 * 60)
                );
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                callback({
                    expired: false,
                    days,
                    hours,
                    minutes,
                    seconds,
                    total: distance,
                });
            }, interval);

            return () => clearInterval(timer);
        },
    },

    /** NOUVEAU: Détection navigateur et appareil (alias x_device) */
    device: {
        /** Informations sur l'appareil */
        info: function () {
            const ua = navigator.userAgent;
            return {
                mobile: /Mobile|Android|iPhone|iPad/.test(ua),
                tablet: /iPad|Android.*Tablet/.test(ua),
                desktop: !/Mobile|Android|iPhone|iPad/.test(ua),
                ios: /iPhone|iPad|iPod/.test(ua),
                android: /Android/.test(ua),
                chrome: /Chrome/.test(ua),
                firefox: /Firefox/.test(ua),
                safari: /Safari/.test(ua) && !/Chrome/.test(ua),
                edge: /Edge/.test(ua),
                touchSupport: "ontouchstart" in window,
            };
        },

        /** Orientation de l'écran */
        orientation: function (callback = null) {
            const current =
                window.innerWidth > window.innerHeight
                    ? "landscape"
                    : "portrait";

            if (callback) {
                window.addEventListener("orientationchange", () => {
                    setTimeout(() => {
                        const newOrientation =
                            window.innerWidth > window.innerHeight
                                ? "landscape"
                                : "portrait";
                        callback(newOrientation);
                    }, 100);
                });
            }

            return current;
        },

        /** Détection connexion */
        connection: function () {
            const conn =
                navigator.connection ||
                navigator.mozConnection ||
                navigator.webkitConnection;
            if (!conn) return null;

            return {
                type: conn.effectiveType,
                downlink: conn.downlink,
                rtt: conn.rtt,
                saveData: conn.saveData,
            };
        },

        /** Vibration (mobile) */
        vibrate: function (pattern = 200) {
            if (navigator.vibrate) {
                navigator.vibrate(pattern);
                return true;
            }
            return false;
        },
    },

    /** NOUVEAU: Cryptographie simple (alias x_crypto) */
    crypto: {
        /** Hash SHA-256 */
        hash: async function (text) {
            const encoder = new TextEncoder();
            const data = encoder.encode(text);
            const hashBuffer = await crypto.subtle.digest("SHA-256", data);
            const hashArray = Array.from(new Uint8Array(hashBuffer));
            return hashArray
                .map((b) => b.toString(16).padStart(2, "0"))
                .join("");
        },

        /** Générer UUID v4 */
        uuid: function () {
            return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(
                /[xy]/g,
                function (c) {
                    const r = (Math.random() * 16) | 0;
                    const v = c === "x" ? r : (r & 0x3) | 0x8;
                    return v.toString(16);
                }
            );
        },

        /** Chiffrement simple (Base64) */
        encode: function (text) {
            return btoa(unescape(encodeURIComponent(text)));
        },

        /** Déchiffrement simple (Base64) */
        decode: function (encoded) {
            try {
                return decodeURIComponent(escape(atob(encoded)));
            } catch (e) {
                console.error("Erreur décodage:", e);
                return null;
            }
        },

        /** Générer token aléatoire */
        token: function (length = 32) {
            const chars =
                "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            let result = "";
            for (let i = 0; i < length; i++) {
                result += chars.charAt(
                    Math.floor(Math.random() * chars.length)
                );
            }
            return result;
        },
    },

    /** NOUVEAU: URL et navigation (alias x_url) */
    url: {
        /** Parser URL */
        parse: function (url = window.location.href) {
            try {
                const parsed = new URL(url);
                return {
                    href: parsed.href,
                    protocol: parsed.protocol,
                    host: parsed.host,
                    hostname: parsed.hostname,
                    port: parsed.port,
                    pathname: parsed.pathname,
                    search: parsed.search,
                    hash: parsed.hash,
                    params: Object.fromEntries(parsed.searchParams),
                };
            } catch (e) {
                return null;
            }
        },

        /** Modifier paramètres URL */
        setParams: function (params, replaceState = true) {
            const url = new URL(window.location);
            Object.entries(params).forEach(([key, value]) => {
                if (value === null || value === undefined) {
                    url.searchParams.delete(key);
                } else {
                    url.searchParams.set(key, value);
                }
            });

            if (replaceState) {
                window.history.replaceState({}, "", url);
            } else {
                window.history.pushState({}, "", url);
            }

            return url.href;
        },

        /** Obtenir paramètre */
        getParam: function (name) {
            return new URLSearchParams(window.location.search).get(name);
        },

        /** Navigation avec historique */
        navigate: function (url, state = {}) {
            window.history.pushState(state, "", url);
            window.dispatchEvent(new PopStateEvent("popstate", { state }));
        },
    },

    /** NOUVEAU: Gestionnaire de fichiers (alias x_files) */
    files: {
        /** Sélecteur de fichier */
        select: function (options = {}) {
            return new Promise((resolve) => {
                const input = X_DOM.create("input", {
                    type: "file",
                    accept: options.accept || "*/*",
                    multiple: options.multiple || false,
                    style: { display: "none" },
                });

                input.addEventListener("change", (e) => {
                    const files = Array.from(e.target.files);
                    resolve(options.multiple ? files : files[0]);
                    input.remove();
                });

                document.body.appendChild(input);
                input.click();
            });
        },

        /** Lire fichier */
        read: function (file, type = "text") {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();

                reader.onload = (e) => resolve(e.target.result);
                reader.onerror = () => reject(reader.error);

                switch (type) {
                    case "text":
                        reader.readAsText(file);
                        break;
                    case "dataURL":
                        reader.readAsDataURL(file);
                        break;
                    case "arrayBuffer":
                        reader.readAsArrayBuffer(file);
                        break;
                    case "binaryString":
                        reader.readAsBinaryString(file);
                        break;
                    default:
                        reader.readAsText(file);
                }
            });
        },

        /** Valider fichier */
        validate: function (file, rules = {}) {
            const errors = [];

            if (rules.maxSize && file.size > rules.maxSize) {
                errors.push(
                    `Fichier trop volumineux (max: ${this.formatSize(
                        rules.maxSize
                    )})`
                );
            }

            if (rules.allowedTypes && !rules.allowedTypes.includes(file.type)) {
                errors.push(
                    `Type de fichier non autorisé (autorisés: ${rules.allowedTypes.join(
                        ", "
                    )})`
                );
            }

            if (rules.allowedExtensions) {
                const ext = file.name.split(".").pop().toLowerCase();
                if (!rules.allowedExtensions.includes(ext)) {
                    errors.push(
                        `Extension non autorisée (autorisées: ${rules.allowedExtensions.join(
                            ", "
                        )})`
                    );
                }
            }

            return {
                valid: errors.length === 0,
                errors: errors,
                file: file,
            };
        },

        /** Formater taille */
        formatSize: function (bytes) {
            const sizes = ["B", "KB", "MB", "GB"];
            if (bytes === 0) return "0 B";
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return (
                Math.round((bytes / Math.pow(1024, i)) * 100) / 100 +
                " " +
                sizes[i]
            );
        },

        /** Upload avec progression */
        upload: function (file, url, options = {}) {
            return new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append(options.fieldName || "file", file);

                const xhr = new XMLHttpRequest();

                xhr.upload.addEventListener("progress", (e) => {
                    if (e.lengthComputable) {
                        const percentage = (e.loaded / e.total) * 100;
                        options.onProgress?.(percentage, e.loaded, e.total);
                    }
                });

                xhr.addEventListener("load", () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            resolve(response);
                        } catch (e) {
                            resolve(xhr.responseText);
                        }
                    } else {
                        reject(new Error(`Upload failed: ${xhr.status}`));
                    }
                });

                xhr.addEventListener("error", () =>
                    reject(new Error("Upload error"))
                );

                xhr.open("POST", url);

                if (options.headers) {
                    Object.entries(options.headers).forEach(([key, value]) => {
                        xhr.setRequestHeader(key, value);
                    });
                }

                xhr.send(formData);
            });
        },
    },

    /** NOUVEAU: Mathématiques et calculs (alias x_math) */
    math: {
        /** Générer nombre aléatoire */
        random: function (min = 0, max = 1, decimals = 0) {
            const rand = Math.random() * (max - min) + min;
            return decimals > 0
                ? parseFloat(rand.toFixed(decimals))
                : Math.floor(rand);
        },

        /** Arrondir avec précision */
        round: function (number, decimals = 0) {
            return parseFloat(number.toFixed(decimals));
        },

        /** Contraindre valeur */
        clamp: function (value, min, max) {
            return Math.min(Math.max(value, min), max);
        },

        /** Interpolation linéaire */
        lerp: function (start, end, factor) {
            return start + (end - start) * factor;
        },

        /** Mapper une valeur d'un range à un autre */
        map: function (value, inMin, inMax, outMin, outMax) {
            return (
                ((value - inMin) * (outMax - outMin)) / (inMax - inMin) + outMin
            );
        },

        /** Calculer pourcentage */
        percentage: function (value, total) {
            return (value / total) * 100;
        },

        /** Moyenne d'un tableau */
        average: function (numbers) {
            return numbers.reduce((sum, num) => sum + num, 0) / numbers.length;
        },
    },

    /** NOUVEAU: Gestion des couleurs (alias x_color) */
    color: {
        /** Convertir HEX vers RGB */
        hexToRgb: function (hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(
                hex
            );
            return result
                ? {
                      r: parseInt(result[1], 16),
                      g: parseInt(result[2], 16),
                      b: parseInt(result[3], 16),
                  }
                : null;
        },

        /** Convertir RGB vers HEX */
        rgbToHex: function (r, g, b) {
            return (
                "#" +
                ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1)
            );
        },

        /** Générer couleur aléatoire */
        random: function (format = "hex") {
            const r = Math.floor(Math.random() * 256);
            const g = Math.floor(Math.random() * 256);
            const b = Math.floor(Math.random() * 256);

            switch (format) {
                case "rgb":
                    return `rgb(${r}, ${g}, ${b})`;
                case "hsl":
                    const hsl = this.rgbToHsl(r, g, b);
                    return `hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)`;
                default:
                    return this.rgbToHex(r, g, b);
            }
        },

        /** Luminosité d'une couleur */
        brightness: function (hex) {
            const rgb = this.hexToRgb(hex);
            if (!rgb) return 0;
            return (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
        },

        /** Contraste automatique */
        contrast: function (backgroundColor) {
            const brightness = this.brightness(backgroundColor);
            return brightness > 128 ? "#000000" : "#ffffff";
        },

        /** Éclaircir/assombrir couleur */
        lighten: function (hex, percent) {
            const rgb = this.hexToRgb(hex);
            if (!rgb) return hex;

            const factor = 1 + percent / 100;
            return this.rgbToHex(
                Math.min(255, Math.floor(rgb.r * factor)),
                Math.min(255, Math.floor(rgb.g * factor)),
                Math.min(255, Math.floor(rgb.b * factor))
            );
        },

        /** RGB vers HSL */
        rgbToHsl: function (r, g, b) {
            r /= 255;
            g /= 255;
            b /= 255;
            const max = Math.max(r, g, b),
                min = Math.min(r, g, b);
            let h,
                s,
                l = (max + min) / 2;

            if (max === min) {
                h = s = 0;
            } else {
                const d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r:
                        h = (g - b) / d + (g < b ? 6 : 0);
                        break;
                    case g:
                        h = (b - r) / d + 2;
                        break;
                    case b:
                        h = (r - g) / d + 4;
                        break;
                }
                h /= 6;
            }

            return {
                h: Math.round(h * 360),
                s: Math.round(s * 100),
                l: Math.round(l * 100),
            };
        },
    },

    /** NOUVEAU: Raccourcis clavier globaux (alias x_hotkeys) */
    hotkeys: {
        _bindings: new Map(),

        /** Enregistrer raccourci */
        register: function (combination, callback, options = {}) {
            const config = {
                preventDefault: true,
                stopPropagation: true,
                ...options,
            };

            const key = this._normalizeCombo(combination);
            this._bindings.set(key, { callback, config });

            if (this._bindings.size === 1) {
                this._initListener();
            }

            return () => this.unregister(combination);
        },

        /** Supprimer raccourci */
        unregister: function (combination) {
            const key = this._normalizeCombo(combination);
            this._bindings.delete(key);

            if (this._bindings.size === 0) {
                this._removeListener();
            }
        },

        /** Nettoyer tous les raccourcis */
        clear: function () {
            this._bindings.clear();
            this._removeListener();
        },

        /** Normaliser combinaison de touches */
        _normalizeCombo: function (combo) {
            return combo
                .toLowerCase()
                .split("+")
                .map((k) => k.trim())
                .sort()
                .join("+");
        },

        /** Initier l'écoute globale */
        _initListener: function () {
            if (!this._listener) {
                this._listener = (e) => this._handleKeydown(e);
                document.addEventListener("keydown", this._listener);
            }
        },

        /** Supprimer l'écoute */
        _removeListener: function () {
            if (this._listener) {
                document.removeEventListener("keydown", this._listener);
                this._listener = null;
            }
        },

        /** Gérer événement clavier */
        _handleKeydown: function (e) {
            const combo = this._getComboFromEvent(e);
            const binding = this._bindings.get(combo);

            if (binding) {
                if (binding.config.preventDefault) e.preventDefault();
                if (binding.config.stopPropagation) e.stopPropagation();
                binding.callback(e);
            }
        },

        /** Extraire combinaison de l'événement */
        _getComboFromEvent: function (e) {
            const parts = [];
            if (e.ctrlKey) parts.push("ctrl");
            if (e.altKey) parts.push("alt");
            if (e.shiftKey) parts.push("shift");
            if (e.metaKey) parts.push("meta");

            const key = e.key.toLowerCase();
            if (!["control", "alt", "shift", "meta"].includes(key)) {
                parts.push(key);
            }

            return parts.sort().join("+");
        },
    },

    /** NOUVEAU: Notifications système (alias x_notifications) */
    notifications: {
        /** Demander permission */
        requestPermission: async function () {
            if (!("Notification" in window)) {
                return "not-supported";
            }

            if (Notification.permission === "granted") {
                return "granted";
            }

            if (Notification.permission === "denied") {
                return "denied";
            }

            const permission = await Notification.requestPermission();
            return permission;
        },

        /** Créer notification */
        create: function (title, options = {}) {
            const config = {
                icon: "",
                body: "",
                tag: "",
                requireInteraction: false,
                ...options,
            };

            if (Notification.permission === "granted") {
                const notification = new Notification(title, config);

                if (options.onClick) {
                    notification.onclick = options.onClick;
                }

                if (options.autoClose) {
                    setTimeout(() => notification.close(), options.autoClose);
                }

                return notification;
            }

            return null;
        },

        /** Notification simple */
        show: function (title, message = "", type = "info") {
            const icons = {
                success: "✅",
                error: "❌",
                warning: "⚠️",
                info: "ℹ️",
            };

            return this.create(title, {
                body: message,
                icon: icons[type] || icons.info,
                autoClose: 5000,
            });
        },
    },

    /** NOUVEAU: Cache intelligent (alias x_smartCache) */
    smartCache: {
        _cache: new Map(),
        _ttl: new Map(),

        /** Mettre en cache avec TTL */
        set: function (key, value, ttl = 300000) {
            // 5 minutes par défaut
            this._cache.set(key, value);
            this._ttl.set(key, Date.now() + ttl);
            return value;
        },

        /** Récupérer du cache */
        get: function (key) {
            if (!this._cache.has(key)) return null;

            const expiry = this._ttl.get(key);
            if (Date.now() > expiry) {
                this.delete(key);
                return null;
            }

            return this._cache.get(key);
        },

        /** Récupérer ou exécuter */
        getOrSet: function (key, factory, ttl = 300000) {
            const cached = this.get(key);
            if (cached !== null) return cached;

            const value = factory();
            return this.set(key, value, ttl);
        },

        /** Récupérer ou exécuter (async) */
        getOrSetAsync: async function (key, factory, ttl = 300000) {
            const cached = this.get(key);
            if (cached !== null) return cached;

            const value = await factory();
            return this.set(key, value, ttl);
        },

        /** Supprimer du cache */
        delete: function (key) {
            this._cache.delete(key);
            this._ttl.delete(key);
        },

        /** Vider le cache */
        clear: function () {
            this._cache.clear();
            this._ttl.clear();
        },

        /** Nettoyer les entrées expirées */
        cleanup: function () {
            const now = Date.now();
            for (const [key, expiry] of this._ttl.entries()) {
                if (now > expiry) {
                    this.delete(key);
                }
            }
        },

        /** Statistiques du cache */
        stats: function () {
            return {
                size: this._cache.size,
                keys: Array.from(this._cache.keys()),
                expired: Array.from(this._ttl.entries())
                    .filter(([key, expiry]) => Date.now() > expiry)
                    .map(([key]) => key),
            };
        },
    },

    /** NOUVEAU: Gestionnaire de thèmes (alias x_themes) */
    themes: {
        _current: "light",

        /** Définir thème */
        set: function (theme) {
            document.body.className = document.body.className.replace(
                /theme-\w+/g,
                ""
            );
            document.body.classList.add(`theme-${theme}`);
            this._current = theme;
            localStorage.setItem("x-dom-theme", theme);

            // Dispatch event
            window.dispatchEvent(
                new CustomEvent("themeChanged", {
                    detail: { theme, previous: this._current },
                })
            );
        },

        /** Obtenir thème actuel */
        get: function () {
            return this._current;
        },

        /** Basculer entre thèmes */
        toggle: function (themes = ["light", "dark"]) {
            const currentIndex = themes.indexOf(this._current);
            const nextIndex = (currentIndex + 1) % themes.length;
            this.set(themes[nextIndex]);
        },

        /** Charger thème sauvegardé */
        load: function () {
            const saved = localStorage.getItem("x-dom-theme");
            if (saved) this.set(saved);
        },

        /** Détecter préférence système */
        detectSystem: function () {
            if (
                window.matchMedia &&
                window.matchMedia("(prefers-color-scheme: dark)").matches
            ) {
                return "dark";
            }
            return "light";
        },

        /** Auto-détection avec sauvegarde */
        auto: function () {
            const saved = localStorage.getItem("x-dom-theme");
            const system = this.detectSystem();
            this.set(saved || system);
        },
    },

    /** NOUVEAU: Performance et debugging (alias x_debug) */
    debug: {
        /** Mesurer temps d'exécution */
        time: function (label, fn) {
            const start = performance.now();
            const result = fn();
            const end = performance.now();
            console.log(`${label}: ${(end - start).toFixed(2)}ms`);
            return result;
        },

        /** Mesurer temps d'exécution async */
        timeAsync: async function (label, fn) {
            const start = performance.now();
            const result = await fn();
            const end = performance.now();
            console.log(`${label}: ${(end - start).toFixed(2)}ms`);
            return result;
        },

        /** Logger avec niveaux */
        log: function (level, message, data = null) {
            const timestamp = new Date().toISOString();
            const prefix = `[${timestamp}] [${level.toUpperCase()}]`;

            switch (level) {
                case "error":
                    console.error(prefix, message, data);
                    break;
                case "warn":
                    console.warn(prefix, message, data);
                    break;
                case "info":
                    console.info(prefix, message, data);
                    break;
                default:
                    console.log(prefix, message, data);
            }
        },

        /** Profiler une fonction */
        profile: function (fn, iterations = 1000) {
            const times = [];

            for (let i = 0; i < iterations; i++) {
                const start = performance.now();
                fn();
                const end = performance.now();
                times.push(end - start);
            }

            return {
                min: Math.min(...times),
                max: Math.max(...times),
                avg: times.reduce((a, b) => a + b) / times.length,
                total: times.reduce((a, b) => a + b),
                iterations: iterations,
            };
        },

        /** Informations système */
        systemInfo: function () {
            return {
                userAgent: navigator.userAgent,
                language: navigator.language,
                platform: navigator.platform,
                cookieEnabled: navigator.cookieEnabled,
                onLine: navigator.onLine,
                screen: {
                    width: screen.width,
                    height: screen.height,
                    colorDepth: screen.colorDepth,
                },
                window: {
                    width: window.innerWidth,
                    height: window.innerHeight,
                },
                memory: performance.memory
                    ? {
                          used: performance.memory.usedJSHeapSize,
                          total: performance.memory.totalJSHeapSize,
                          limit: performance.memory.jsHeapSizeLimit,
                      }
                    : null,
            };
        },
    },

    // ======================== INTERNE ========================
    _fnCache: {},
    _components: {},

    _createToastContainer() {
        const container = this.create("div", {
            id: "xdom-toast-container",
            className: "xdom-toast-container",
            style: {
                position: "fixed",
                top: "20px",
                right: "20px",
                zIndex: "10000",
            },
        });
        this.append(document.body, container);
        return container;
    },

    _removeToast(toast) {
        this.effect(toast, "fadeOut", { duration: 200 });
        setTimeout(() => toast.remove(), 200);
    },

    _closeModal(modal, config) {
        if (config.animation) {
            this.effect(modal, "fadeOut", { duration: 200 });
            setTimeout(() => this.classAction(modal, "open", "remove"), 200);
        } else {
            this.classAction(modal, "open", "remove");
        }
        config.onClose?.(modal);
    },

    _fetchReload: async function (el, config) {
        try {
            const html = await this.fetch(config.url);
            this._applyReload(el, html, config);
            config.after?.(el);
            return el;
        } catch (err) {
            console.error("Erreur rechargement:", err);
            return false;
        }
    },

    _applyReload: function (el, content, config) {
        switch (config.method) {
            case "replaceWith":
                el.replaceWith(content);
                break;
            case "outerHTML":
                el.outerHTML = content;
                break;
            default:
                el.innerHTML = content;
        }
        if (config.animation) this.animate(el, config.animation);
    },

    _executeWithRetry: async function (fn, config, attempt = 0) {
        try {
            const result = await fn(...config.args);
            if (config.cache)
                this._fnCache[config.cacheKey || fn.name] = result;
            return result;
        } catch (err) {
            if (attempt >= config.retries) throw err;
            await new Promise((r) => setTimeout(r, config.delay));
            return this._executeWithRetry(fn, config, attempt + 1);
        }
    },

    _getElement(idOrEl) {
        return typeof idOrEl === "string"
            ? document.getElementById(idOrEl)
            : idOrEl;
    },

    // ======================== INITIALISATION ========================
    /** NOUVEAU: Auto-initialisation (alias x_init) */
    init: function (config = {}) {
        const defaults = {
            theme: true,
            lazyImages: true,
            modals: true,
            tooltips: true,
            smoothScroll: true,
        };

        const settings = { ...defaults, ...config };

        document.addEventListener("DOMContentLoaded", () => {
            // Auto-initialisation du thème
            if (settings.theme) {
                const savedTheme = this.storage("xdom_theme");
                if (savedTheme) {
                    this.theme.set(savedTheme, false);
                } else if (settings.autoTheme) {
                    this.theme.auto();
                }
            }

            // Auto-initialisation des images lazy
            if (settings.lazyImages) {
                this.perf.lazyImages();
            }

            // Auto-initialisation des modales
            if (settings.modals) {
                document.querySelectorAll("[data-modal]").forEach((modal) => {
                    this.modal(modal.id || modal.dataset.modal);
                });
            }

            // Smooth scroll
            if (settings.smoothScroll) {
                this.css(document.documentElement, {
                    scrollBehavior: "smooth",
                });
            }

            console.log("🚀 X_DOM v3.0 initialisé");
        });
    },

    addEventListener: function (event, callback) {
        document.addEventListener(event, callback);
    },
};

// ======================== ALIAS EXISTANTS ========================
const x_ = X_DOM._.bind(X_DOM);
const x_class = X_DOM.class.bind(X_DOM);
const x_selectQuery = X_DOM.query.bind(X_DOM);
const x_you_selectQuery = X_DOM.xquery.bind(X_DOM);
const x_textContent = X_DOM.text.bind(X_DOM);
const x_textContents = X_DOM.texts.bind(X_DOM);
const x_inner = X_DOM.html.bind(X_DOM);
const x_inners = X_DOM.htmls.bind(X_DOM);
const x_val = X_DOM.val.bind(X_DOM);
const x_vals = X_DOM.vals.bind(X_DOM);
const x_create = X_DOM.create.bind(X_DOM);
const x_multi_create = X_DOM.createMultiple.bind(X_DOM);
const x_append = X_DOM.append.bind(X_DOM);
const x_prepend = X_DOM.prepend.bind(X_DOM);
const x_remove = X_DOM.remove.bind(X_DOM);
const x_classAction = X_DOM.classAction.bind(X_DOM);
const x_css = X_DOM.css.bind(X_DOM);
const x_attr = X_DOM.attr.bind(X_DOM);
const x_animate = X_DOM.animate.bind(X_DOM);
const x_event = X_DOM.on.bind(X_DOM);
const x_multi_event = X_DOM.onMultiple.bind(X_DOM);
const x_debounce = X_DOM.debounce.bind(X_DOM);
const x_fetch = X_DOM.fetch.bind(X_DOM);
const x_storage = X_DOM.storage.bind(X_DOM);
const x_observe = X_DOM.observe.bind(X_DOM);
const x_draggable = X_DOM.draggable.bind(X_DOM);
const x_formData = X_DOM.formData.bind(X_DOM);
const x_jParse = X_DOM.parse.bind(X_DOM);
const x_jStringify = X_DOM.stringify.bind(X_DOM);
const x_multi = X_DOM.batch.bind(X_DOM);
const x_datatable = X_DOM.datatable.bind(X_DOM);
const x_reload = X_DOM.reload.bind(X_DOM);
const x_multiply = X_DOM.multiply.bind(X_DOM);
const x_generateForm = X_DOM.generateForm.bind(X_DOM);
const x_initTabs = X_DOM.initTabs.bind(X_DOM);
const x_createNested = X_DOM.createNested.bind(X_DOM);
const x_renderList = X_DOM.renderList.bind(X_DOM);
const x_onClickOutside = X_DOM.onClickOutside.bind(X_DOM);
const x_copyToClipboard = X_DOM.copyToClipboard.bind(X_DOM);
const x_modal = X_DOM.modal.bind(X_DOM);
const x_reloadElement = X_DOM.reloadElement.bind(X_DOM);
const x_reloadFunction = X_DOM.reloadFunction.bind(X_DOM);
const x_user = X_DOM.user.bind(X_DOM);
const x_number_format = X_DOM.number_format.bind(X_DOM);
const x_verifier_string = X_DOM.verifierString.bind(X_DOM);

// ======================== NOUVEAUX ALIAS ========================
const x_safe = X_DOM.safe.bind(X_DOM);
const x_waitFor = X_DOM.waitFor.bind(X_DOM);
const x_template = X_DOM.template.bind(X_DOM);
const x_fragment = X_DOM.fragment.bind(X_DOM);
const x_empty = X_DOM.empty.bind(X_DOM);
const x_replace = X_DOM.replace.bind(X_DOM);
const x_toggleClass = X_DOM.toggleClass.bind(X_DOM);
const x_cssVar = X_DOM.cssVar.bind(X_DOM);
const x_removeAttr = X_DOM.removeAttr.bind(X_DOM);
const x_transition = X_DOM.transition.bind(X_DOM);
const x_effect = X_DOM.effect.bind(X_DOM);
const x_delegate = X_DOM.delegate.bind(X_DOM);
const x_once = X_DOM.once.bind(X_DOM);
const x_throttle = X_DOM.throttle.bind(X_DOM);
const x_fetchAll = X_DOM.fetchAll.bind(X_DOM);
const x_cache = X_DOM.cache.bind(X_DOM);
const x_intersect = X_DOM.intersect.bind(X_DOM);
const x_resize = X_DOM.resize.bind(X_DOM);
const x_dropzone = X_DOM.dropzone.bind(X_DOM);
const x_validate = X_DOM.validate.bind(X_DOM);
const x_table = X_DOM.table.bind(X_DOM);
const x_toast = X_DOM.toast.bind(X_DOM);
const x_loader = X_DOM.loader.bind(X_DOM);
const x_progress = X_DOM.progress.bind(X_DOM);
const x_component = X_DOM.component.bind(X_DOM);

// Alias pour les modules
const x_utils = X_DOM.utils;
const x_state = X_DOM.state.bind(X_DOM);
const x_media = X_DOM.media;
const x_cookies = X_DOM.cookies;
const x_theme = X_DOM.theme;
const x_perf = X_DOM.perf;
const x_a11y = X_DOM.a11y;
const x_worker = X_DOM.worker;
const x_init = X_DOM.init.bind(X_DOM);
const x_addEventListener = X_DOM.addEventListener.bind(X_DOM);

// ======================== ALIAS POUR NOUVELLES FONCTIONNALITÉS v3.0 ========================

// Canvas
const x_canvas = X_DOM.canvas;
const x_canvasCreate = X_DOM.canvas.create.bind(X_DOM.canvas);
const x_canvasDraw = X_DOM.canvas.draw;
const x_canvasUtils = X_DOM.canvas.utils;
const x_canvasDrawing = X_DOM.canvas.drawing;

// Géolocalisation
const x_geo = X_DOM.geo;
const x_getCurrentPosition = X_DOM.geo.getCurrentPosition.bind(X_DOM.geo);
const x_watchPosition = X_DOM.geo.watchPosition.bind(X_DOM.geo);
const x_geoDistance = X_DOM.geo.distance.bind(X_DOM.geo);

// Audio
const x_audio = X_DOM.audio;
const x_audioCreate = X_DOM.audio.create.bind(X_DOM.audio);
const x_audioNotify = X_DOM.audio.notify.bind(X_DOM.audio);

// Date et temps
const x_date = X_DOM.date;
const x_dateFormat = X_DOM.date.format.bind(X_DOM.date);
const x_dateDiff = X_DOM.date.diff.bind(X_DOM.date);
const x_dateRelative = X_DOM.date.relative.bind(X_DOM.date);
const x_countdown = X_DOM.date.countdown.bind(X_DOM.date);

// Device
const x_device = X_DOM.device;
const x_deviceInfo = X_DOM.device.info.bind(X_DOM.device);
const x_orientation = X_DOM.device.orientation.bind(X_DOM.device);
const x_connection = X_DOM.device.connection.bind(X_DOM.device);
const x_vibrate = X_DOM.device.vibrate.bind(X_DOM.device);

// Cryptographie
const x_crypto = X_DOM.crypto;
const x_hash = X_DOM.crypto.hash.bind(X_DOM.crypto);
const x_uuid = X_DOM.crypto.uuid.bind(X_DOM.crypto);
const x_encode = X_DOM.crypto.encode.bind(X_DOM.crypto);
const x_decode = X_DOM.crypto.decode.bind(X_DOM.crypto);
const x_token = X_DOM.crypto.token.bind(X_DOM.crypto);

// URL
const x_url = X_DOM.url;
const x_urlParse = X_DOM.url.parse.bind(X_DOM.url);
const x_setParams = X_DOM.url.setParams.bind(X_DOM.url);
const x_getParam = X_DOM.url.getParam.bind(X_DOM.url);
const x_navigate = X_DOM.url.navigate.bind(X_DOM.url);

// Files
const x_files = X_DOM.files;
const x_fileSelect = X_DOM.files.select.bind(X_DOM.files);
const x_fileRead = X_DOM.files.read.bind(X_DOM.files);
const x_fileValidate = X_DOM.files.validate.bind(X_DOM.files);
const x_fileUpload = X_DOM.files.upload.bind(X_DOM.files);
const x_formatSize = X_DOM.files.formatSize.bind(X_DOM.files);

// Math
const x_math = X_DOM.math;
const x_random = X_DOM.math.random.bind(X_DOM.math);
const x_round = X_DOM.math.round.bind(X_DOM.math);
const x_clamp = X_DOM.math.clamp.bind(X_DOM.math);
const x_lerp = X_DOM.math.lerp.bind(X_DOM.math);
const x_map = X_DOM.math.map.bind(X_DOM.math);
const x_percentage = X_DOM.math.percentage.bind(X_DOM.math);
const x_average = X_DOM.math.average.bind(X_DOM.math);

// Color
const x_color = X_DOM.color;
const x_hexToRgb = X_DOM.color.hexToRgb.bind(X_DOM.color);
const x_rgbToHex = X_DOM.color.rgbToHex.bind(X_DOM.color);
const x_randomColor = X_DOM.color.random.bind(X_DOM.color);
const x_brightness = X_DOM.color.brightness.bind(X_DOM.color);
const x_contrast = X_DOM.color.contrast.bind(X_DOM.color);
const x_lighten = X_DOM.color.lighten.bind(X_DOM.color);
const x_rgbToHsl = X_DOM.color.rgbToHsl.bind(X_DOM.color);

// Hotkeys
const x_hotkeys = X_DOM.hotkeys;
const x_registerHotkey = X_DOM.hotkeys.register.bind(X_DOM.hotkeys);
const x_unregisterHotkey = X_DOM.hotkeys.unregister.bind(X_DOM.hotkeys);
const x_clearHotkeys = X_DOM.hotkeys.clear.bind(X_DOM.hotkeys);

// Notifications
const x_notifications = X_DOM.notifications;
const x_requestNotification = X_DOM.notifications.requestPermission.bind(
    X_DOM.notifications
);
const x_createNotification = X_DOM.notifications.create.bind(
    X_DOM.notifications
);
const x_showNotification = X_DOM.notifications.show.bind(X_DOM.notifications);

// Smart Cache
const x_smartCache = X_DOM.smartCache;
const x_cacheSet = X_DOM.smartCache.set.bind(X_DOM.smartCache);
const x_cacheGet = X_DOM.smartCache.get.bind(X_DOM.smartCache);
const x_cacheGetOrSet = X_DOM.smartCache.getOrSet.bind(X_DOM.smartCache);
const x_cacheGetOrSetAsync = X_DOM.smartCache.getOrSetAsync.bind(
    X_DOM.smartCache
);
const x_cacheDelete = X_DOM.smartCache.delete.bind(X_DOM.smartCache);
const x_cacheClear = X_DOM.smartCache.clear.bind(X_DOM.smartCache);
const x_cacheCleanup = X_DOM.smartCache.cleanup.bind(X_DOM.smartCache);
const x_cacheStats = X_DOM.smartCache.stats.bind(X_DOM.smartCache);

// Themes
const x_themes = X_DOM.themes;
const x_setTheme = X_DOM.themes.set.bind(X_DOM.themes);
const x_getTheme = X_DOM.themes.get.bind(X_DOM.themes);
const x_toggleTheme = X_DOM.themes.toggle.bind(X_DOM.themes);
const x_loadTheme = X_DOM.themes.load.bind(X_DOM.themes);
const x_detectTheme = X_DOM.themes.detectSystem.bind(X_DOM.themes);
const x_autoTheme = X_DOM.themes.auto.bind(X_DOM.themes);

// Debug
const x_debug = X_DOM.debug;
const x_time = X_DOM.debug.time.bind(X_DOM.debug);
const x_timeAsync = X_DOM.debug.timeAsync.bind(X_DOM.debug);
const x_debugLog = X_DOM.debug.log.bind(X_DOM.debug);
const x_profile = X_DOM.debug.profile.bind(X_DOM.debug);
const x_systemInfo = X_DOM.debug.systemInfo.bind(X_DOM.debug);

// ======================== EXTENSIONS CSS AUTOMATIQUES ========================
// Injection automatique des styles essentiels
if (typeof document !== "undefined") {
    const styles = `
        /* X_DOM Styles par défaut */
        .xdom-toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .xdom-toast {
            padding: 12px 16px;
            border-radius: 6px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 300px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .xdom-toast.show { transform: translateX(0); }
        .xdom-toast-info { background: #3498db; }
        .xdom-toast-success { background: #2ecc71; }
        .xdom-toast-warning { background: #f39c12; }
        .xdom-toast-error { background: #e74c3c; }
        
        .xdom-toast-close {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            margin-left: 10px;
            opacity: 0.7;
        }
        .xdom-toast-close:hover { opacity: 1; }
        
        .xdom-loader {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .xdom-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: xdom-spin 1s linear infinite;
        }
        
        @keyframes xdom-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .xdom-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }
        
        .xdom-table th,
        .xdom-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .xdom-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .xdom-table tr:hover {
            background: #f8f9fa;
        }
        
        .drag-hover {
            border: 2px dashed #3498db !important;
            background: rgba(52, 152, 219, 0.1) !important;
        }
        
        img.lazy {
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        img.loaded {
            opacity: 1;
        }
        
        /* Utilitaires d'accessibilité */
        .sr-only {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
        
        .focus-visible {
            outline: 2px solid #3498db !important;
            outline-offset: 2px !important;
        }
    `;

    // Injecter les styles si pas déjà présents
    if (!document.querySelector("#xdom-styles")) {
        const styleSheet = document.createElement("style");
        styleSheet.id = "xdom-styles";
        styleSheet.textContent = styles;
        document.head.appendChild(styleSheet);
    }
}

/** Export */

// Export pour le navigateur
if (typeof window !== "undefined") {
    window.X_DOM = X_DOM;
}

// Export pour les modules
if (typeof module !== "undefined" && module.exports) {
    module.exports = X_DOM;
}

// Auto-initialisation optionnelle
if (typeof window !== "undefined" && window.X_DOM_AUTO_INIT !== false) {
    X_DOM.init();
}
