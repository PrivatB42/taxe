/**
 * XEditor - Éditeur de texte avancé basé sur Quill
 * @version 1.0.0
 * @license MIT
 * Dépendance: Quill.js (https://cdn.quilljs.com/)
 */

class XEditor {
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        if (!this.container) {
            throw new Error(`Container #${containerId} non trouvé`);
        }

        // Configuration par défaut
        this.config = {
            theme: "snow",
            placeholder: "Commencez à écrire...",
            readOnly: false,
            debug: false,
            saveCallback: null,
            bounds: this.container,
            scrollingContainer: null,
            formats: [
                "background",
                "bold",
                "color",
                "font",
                "code",
                "italic",
                "link",
                "size",
                "strike",
                "script",
                "underline",
                "blockquote",
                "header",
                "indent",
                "list",
                "align",
                "direction",
                "code-block",
                "formula",
                "image",
                "video",
                "table",
                "clean",
            ],
            modules: {
                toolbar: {
                    container: null, // Will be set dynamically
                    handlers: {},
                },
                history: {
                    delay: 2000,
                    maxStack: 500,
                    userOnly: true,
                },
                clipboard: {
                    matchVisual: false,
                },
                keyboard: {
                    bindings: {},
                },
            },
            // Options XEditor spécifiques
            autosave: true,
            autosaveInterval: 30000,
            wordCount: true,
            spellcheck: true,
            templates: [],
            customFonts: [],
            mathSupport: false,
            tableSupport: true,
            imageUpload: null,
            maxLength: null,
            ...options,
        };

        this.quill = null;
        this.toolbar = null;
        this.statusBar = null;
        this.wordCount = 0;
        this.charCount = 0;
        this.autosaveTimer = null;
        this.plugins = new Map();

        this.init();
    }

    // ======================== INITIALISATION ========================

    init() {
        this.createEditorStructure();
        this.createCustomToolbar();
        this.initializeQuill();
        this.setupEventListeners();
        this.setupAutosave();
        this.setupWordCount();
        this.loadCustomModules();
        this.applyCustomStyles();

        console.log("🚀 XEditor initialisé");
    }

    createEditorStructure() {
        this.container.innerHTML = `
            <div class="xeditor-wrapper">
                <div class="xeditor-toolbar" id="${this.containerId}-toolbar">
                    <!-- Toolbar sera généré dynamiquement -->
                </div>
                <div class="xeditor-content">
                    <div class="xeditor-editor" id="${this.containerId}-editor"></div>
                </div>
                <div class="xeditor-status-bar" id="${this.containerId}-status">
                    <div class="xeditor-stats">
                        <span class="word-count">Mots: 0</span>
                        <span class="char-count">Caractères: 0</span>
                    </div>
                    <div class="xeditor-actions">
                        <button class="btn-fullscreen" title="Plein écran">⛶</button>
                        <button class="btn-export" title="Exporter">📄</button>
                        <button class="btn-save" title="Sauvegarder">💾</button>
                    </div>
                </div>
            </div>
        `;

        this.toolbar = document.getElementById(`${this.containerId}-toolbar`);
        this.statusBar = document.getElementById(`${this.containerId}-status`);
    }

    createCustomToolbar() {
        const toolbarConfig = [
            // Première ligne - Formatage de base
            [
                { header: ["1", "2", "3", "4", "5", "6", false] },
                {
                    font: this.config.customFonts.length
                        ? this.config.customFonts
                        : [],
                },
                { size: ["small", false, "large", "huge"] },
            ],
            [
                "bold",
                "italic",
                "underline",
                "strike",
                { script: "sub" },
                { script: "super" },
                "code",
            ],
            [
                { color: [] },
                { background: [] },
                { align: [] },
                { indent: "-1" },
                { indent: "+1" },
            ],
            [
                { list: "ordered" },
                { list: "bullet" },
                "blockquote",
                "code-block",
            ],
            [
                "link",
                "image",
                "video",
                ...(this.config.tableSupport ? ["table"] : []),
                ...(this.config.mathSupport ? ["formula"] : []),
            ],
            ["clean", "undo", "redo", "template", "export", "fullscreen"],
        ];

        this.config.modules.toolbar.container = toolbarConfig;
        this.config.modules.toolbar.handlers = {
            undo: () => this.undo(),
            redo: () => this.redo(),
            template: () => this.showTemplateDialog(),
            export: () => this.showExportDialog(),
            fullscreen: () => this.toggleFullscreen(),
            table: () => this.insertTable(),
        };
    }

    initializeQuill() {
        // Vérifier si Quill est disponible
        if (typeof Quill === "undefined") {
            console.error(
                "Quill.js n'est pas chargé. Veuillez inclure Quill.js avant XEditor."
            );
            return;
        }

        // Enregistrer les modules personnalisés
        this.registerCustomModules();

        // Initialiser Quill
        this.quill = new Quill(`#${this.containerId}-editor`, this.config);

        // Configuration supplémentaire
        if (this.config.maxLength) {
            this.setupMaxLength();
        }
    }

    registerCustomModules() {
        // Module Undo/Redo personnalisé
        const icons = Quill.import("ui/icons");
        icons["undo"] = "↶";
        icons["redo"] = "↷";
        icons["template"] = "📋";
        icons["export"] = "📤";
        icons["fullscreen"] = "⛶";
        icons["table"] = "⊞";

        // Module Table personnalisé
        if (this.config.tableSupport) {
            this.registerTableModule();
        }

        // Module Math personnalisé
        if (this.config.mathSupport) {
            this.registerMathModule();
        }
    }

    registerTableModule() {
        const BlockEmbed = Quill.import("blots/block/embed");

        class TableBlot extends BlockEmbed {
            static create(value) {
                const node = super.create();
                node.innerHTML = value.html || this.defaultTable();
                node.setAttribute("contenteditable", true);
                return node;
            }

            static value(node) {
                return {
                    html: node.innerHTML,
                };
            }

            static defaultTable() {
                return `
                    <table class="xeditor-table">
                        <tr><td>Cellule 1</td><td>Cellule 2</td></tr>
                        <tr><td>Cellule 3</td><td>Cellule 4</td></tr>
                    </table>
                `;
            }
        }

        TableBlot.blotName = "table";
        TableBlot.tagName = "div";
        TableBlot.className = "table-container";

        Quill.register(TableBlot);
    }

    registerMathModule() {
        const Embed = Quill.import("blots/embed");

        class FormulaBlot extends Embed {
            static create(value) {
                const node = super.create();
                if (typeof katex !== "undefined") {
                    katex.render(value, node, {
                        throwOnError: false,
                        errorColor: "#cc0000",
                    });
                } else {
                    node.innerHTML = `\\(${value}\\)`;
                }
                node.setAttribute("data-value", value);
                return node;
            }

            static value(node) {
                return node.getAttribute("data-value");
            }
        }

        FormulaBlot.blotName = "formula";
        FormulaBlot.tagName = "span";
        FormulaBlot.className = "ql-formula";

        Quill.register(FormulaBlot);
    }

    // ======================== ÉVÉNEMENTS ========================

    setupEventListeners() {
        // Événements Quill
        this.quill.on("text-change", (delta, oldDelta, source) => {
            if (source === "user") {
                this.updateWordCount();
                this.triggerAutosave();
                this.emit("text-change", { delta, oldDelta, source });
            }
        });

        this.quill.on("selection-change", (range, oldRange, source) => {
            this.emit("selection-change", { range, oldRange, source });
        });

        // Événements boutons status bar
        this.setupStatusBarEvents();

        // Raccourcis clavier
        this.setupKeyboardShortcuts();
    }

    setupStatusBarEvents() {
        const fullscreenBtn = this.statusBar.querySelector(".btn-fullscreen");
        const exportBtn = this.statusBar.querySelector(".btn-export");
        const saveBtn = this.statusBar.querySelector(".btn-save");

        fullscreenBtn?.addEventListener("click", () => this.toggleFullscreen());
        exportBtn?.addEventListener("click", () => this.showExportDialog());
        saveBtn?.addEventListener("click", () => this.save());
    }

    setupKeyboardShortcuts() {
        const bindings = {
            "ctrl+s": {
                key: "s",
                ctrlKey: true,
                handler: (range, context) => {
                    this.save();
                    return false;
                },
            },
            "ctrl+z": {
                key: "z",
                ctrlKey: true,
                handler: (range, context) => {
                    this.undo();
                    return false;
                },
            },
            "ctrl+y": {
                key: "y",
                ctrlKey: true,
                handler: (range, context) => {
                    this.redo();
                    return false;
                },
            },
            f11: {
                key: "F11",
                handler: (range, context) => {
                    this.toggleFullscreen();
                    return false;
                },
            },
        };

        Object.keys(bindings).forEach((key) => {
            this.config.modules.keyboard.bindings[key] = bindings[key];
        });
    }

    // ======================== FONCTIONNALITÉS PRINCIPALES ========================

    // Gestion du contenu
    getContent(format = "html") {
        switch (format) {
            case "html":
                return this.quill.root.innerHTML;
            case "text":
                return this.quill.getText();
            case "delta":
                return this.quill.getContents();
            case "json":
                return JSON.stringify(this.quill.getContents());
            default:
                return this.quill.root.innerHTML;
        }
    }

    setContent(content, format = "html") {
        switch (format) {
            case "html":
                this.quill.root.innerHTML = content;
                break;
            case "text":
                this.quill.setText(content);
                break;
            case "delta":
                this.quill.setContents(content);
                break;
            case "json":
                this.quill.setContents(JSON.parse(content));
                break;
        }
        this.updateWordCount();
    }

    // Historique
    undo() {
        this.quill.history.undo();
    }

    redo() {
        this.quill.history.redo();
    }

    // Formatage
    format(name, value = true) {
        const range = this.quill.getSelection();
        if (range) {
            this.quill.formatText(range.index, range.length, name, value);
        }
    }

    // Insertion
    insertText(text, index = null) {
        const insertIndex =
            index !== null ? index : this.quill.getSelection()?.index || 0;
        this.quill.insertText(insertIndex, text);
    }

    insertEmbed(type, value, index = null) {
        const insertIndex =
            index !== null ? index : this.quill.getSelection()?.index || 0;
        this.quill.insertEmbed(insertIndex, type, value);
    }

    insertTable(rows = 2, cols = 2) {
        let tableHTML = '<table class="xeditor-table">';
        for (let i = 0; i < rows; i++) {
            tableHTML += "<tr>";
            for (let j = 0; j < cols; j++) {
                tableHTML += `<td>Cellule ${i + 1}-${j + 1}</td>`;
            }
            tableHTML += "</tr>";
        }
        tableHTML += "</table>";

        this.insertEmbed("table", { html: tableHTML });
    }

    // Images
    insertImage(url, alt = "") {
        this.insertEmbed("image", url);
    }

    uploadImage(file) {
        if (
            this.config.imageUpload &&
            typeof this.config.imageUpload === "function"
        ) {
            return this.config.imageUpload(file);
        }

        // Upload par défaut (base64)
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.insertImage(e.target.result);
                resolve(e.target.result);
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    // ======================== TEMPLATES ========================

    addTemplate(name, content, preview = "") {
        this.config.templates.push({
            name,
            content,
            preview: preview || content.substring(0, 100) + "...",
            id: Date.now().toString(),
        });
    }

    showTemplateDialog() {
        const dialog = this.createDialog(
            "Templates",
            this.createTemplateList()
        );
        document.body.appendChild(dialog);
    }

    createTemplateList() {
        const container = document.createElement("div");
        container.className = "template-list";

        this.config.templates.forEach((template) => {
            const item = document.createElement("div");
            item.className = "template-item";
            item.innerHTML = `
                <h4>${template.name}</h4>
                <p>${template.preview}</p>
                <button onclick="window.xeditorInstance?.applyTemplate('${template.id}'); this.closest('.xeditor-dialog').remove();">
                    Utiliser
                </button>
            `;
            container.appendChild(item);
        });

        return container;
    }

    applyTemplate(templateId) {
        const template = this.config.templates.find((t) => t.id === templateId);
        if (template) {
            this.setContent(template.content);
        }
    }

    // ======================== EXPORT ========================

    showExportDialog() {
        const dialog = this.createDialog(
            "Exporter",
            this.createExportOptions()
        );
        document.body.appendChild(dialog);
    }

    createExportOptions() {
        const container = document.createElement("div");
        container.className = "export-options";
        container.innerHTML = `
            <div class="export-format">
                <label><input type="radio" name="format" value="html" checked> HTML</label>
                <label><input type="radio" name="format" value="pdf"> PDF</label>
                <label><input type="radio" name="format" value="docx"> Word (.docx)</label>
                <label><input type="radio" name="format" value="txt"> Texte brut</label>
            </div>
            <div class="export-options-advanced">
                <label><input type="checkbox" id="includeStyles"> Inclure les styles</label>
                <label><input type="checkbox" id="includeImages"> Inclure les images</label>
            </div>
            <div class="export-actions">
                <button onclick="window.xeditorInstance?.exportContent(); this.closest('.xeditor-dialog').remove();">
                    Télécharger
                </button>
                <button onclick="this.closest('.xeditor-dialog').remove();">
                    Annuler
                </button>
            </div>
        `;

        return container;
    }

    exportContent() {
        const format =
            document.querySelector('input[name="format"]:checked')?.value ||
            "html";
        const filename = `document_${new Date().getTime()}`;

        switch (format) {
            case "html":
                this.downloadHTML(filename);
                break;
            case "pdf":
                this.downloadPDF(filename);
                break;
            case "docx":
                this.downloadDOCX(filename);
                break;
            case "txt":
                this.downloadText(filename);
                break;
        }
    }

    downloadHTML(filename) {
        const content = this.getContent("html");
        const html = `
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>${filename}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 40px; }
        .ql-editor { font-size: 14px; }
        ${this.getEditorStyles()}
    </style>
</head>
<body>
    <div class="ql-editor">${content}</div>
</body>
</html>`;

        this.downloadFile(html, `${filename}.html`, "text/html");
    }

    downloadPDF(filename) {
        if (typeof html2pdf !== "undefined") {
            const element = document.createElement("div");
            element.innerHTML = this.getContent("html");

            html2pdf()
                .from(element)
                .set({
                    margin: 1,
                    filename: `${filename}.pdf`,
                    html2canvas: { scale: 2 },
                    jsPDF: {
                        orientation: "portrait",
                        unit: "in",
                        format: "letter",
                        compressPDF: true,
                    },
                })
                .save();
        } else {
            alert(
                "html2pdf.js n'est pas chargé. Veuillez inclure la bibliothèque pour l'export PDF."
            );
        }
    }

    downloadDOCX(filename) {
        // Nécessite une bibliothèque comme docx.js
        console.warn("Export DOCX nécessite une bibliothèque supplémentaire");
        this.downloadHTML(filename); // Fallback
    }

    downloadText(filename) {
        const content = this.getContent("text");
        this.downloadFile(content, `${filename}.txt`, "text/plain");
    }

    downloadFile(content, filename, mimeType) {
        const blob = new Blob([content], { type: mimeType });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    // ======================== UTILITAIRES ========================

    updateWordCount() {
        const text = this.getContent("text");
        this.wordCount = text.trim() ? text.trim().split(/\s+/).length : 0;
        this.charCount = text.length;

        const wordSpan = this.statusBar.querySelector(".word-count");
        const charSpan = this.statusBar.querySelector(".char-count");

        if (wordSpan) wordSpan.textContent = `Mots: ${this.wordCount}`;
        if (charSpan) charSpan.textContent = `Caractères: ${this.charCount}`;
    }

    setupWordCount() {
        if (this.config.wordCount) {
            this.updateWordCount();
        }
    }

    setupAutosave() {
        if (this.config.autosave) {
            this.autosaveTimer = setInterval(() => {
                this.autosave();
            }, this.config.autosaveInterval);
        }
    }

    autosave() {
        const content = this.getContent("json");
        localStorage.setItem(`xeditor_autosave_${this.containerId}`, content);
        console.log("💾 Sauvegarde automatique effectuée");
    }

    triggerAutosave() {
        // Autosave avec debounce
        clearTimeout(this._autosaveDebounce);
        this._autosaveDebounce = setTimeout(() => {
            if (this.config.autosave) {
                this.autosave();
            }
        }, 5000);
    }

    loadAutosave() {
        const saved = localStorage.getItem(
            `xeditor_autosave_${this.containerId}`
        );
        if (saved) {
            try {
                this.setContent(saved, "json");
                return true;
            } catch (e) {
                console.error("Erreur lors du chargement de la sauvegarde:", e);
            }
        }
        return false;
    }

    setupMaxLength() {
        this.quill.on("text-change", () => {
            const text = this.quill.getText();
            if (text.length > this.config.maxLength) {
                this.quill.deleteText(this.config.maxLength, text.length);
            }
        });
    }

    toggleFullscreen() {
        this.container.classList.toggle("xeditor-fullscreen");
        if (this.container.classList.contains("xeditor-fullscreen")) {
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = "";
        }
    }

    // ======================== PLUGINS ========================

    registerPlugin(name, plugin) {
        this.plugins.set(name, plugin);
        if (typeof plugin.init === "function") {
            plugin.init(this);
        }
    }

    unregisterPlugin(name) {
        const plugin = this.plugins.get(name);
        if (plugin && typeof plugin.destroy === "function") {
            plugin.destroy();
        }
        this.plugins.delete(name);
    }

    // ======================== ÉVÉNEMENTS PERSONNALISÉS ========================

    on(event, callback) {
        if (!this._events) this._events = {};
        if (!this._events[event]) this._events[event] = [];
        this._events[event].push(callback);
    }

    off(event, callback) {
        if (!this._events || !this._events[event]) return;
        const index = this._events[event].indexOf(callback);
        if (index > -1) {
            this._events[event].splice(index, 1);
        }
    }

    emit(event, data) {
        if (!this._events || !this._events[event]) return;
        this._events[event].forEach((callback) => callback(data));
    }

    // ======================== UTILITAIRES UI ========================

    createDialog(title, content) {
        const dialog = document.createElement("div");
        dialog.className = "xeditor-dialog";
        dialog.innerHTML = `
            <div class="xeditor-dialog-overlay">
                <div class="xeditor-dialog-content">
                    <div class="xeditor-dialog-header">
                        <h3>${title}</h3>
                        <button class="xeditor-dialog-close">×</button>
                    </div>
                    <div class="xeditor-dialog-body"></div>
                </div>
            </div>
        `;

        const body = dialog.querySelector(".xeditor-dialog-body");
        body.appendChild(content);

        // Event listeners
        dialog
            .querySelector(".xeditor-dialog-close")
            .addEventListener("click", () => {
                dialog.remove();
            });

        dialog
            .querySelector(".xeditor-dialog-overlay")
            .addEventListener("click", (e) => {
                if (
                    e.target === dialog.querySelector(".xeditor-dialog-overlay")
                ) {
                    dialog.remove();
                }
            });

        return dialog;
    }

    // ======================== STYLES ========================

    loadCustomModules() {
        // Charger des modules personnalisés si nécessaire
    }

    getEditorStyles() {
        return `
            .ql-toolbar { border-top: none; border-left: none; border-right: none; }
            .ql-container { border-left: none; border-right: none; border-bottom: none; }
            .xeditor-table { border-collapse: collapse; width: 100%; }
            .xeditor-table td { border: 1px solid #ddd; padding: 8px; }
            .ql-formula { background: #f0f0f0; padding: 2px 4px; border-radius: 3px; }
        `;
    }

    applyCustomStyles() {
        const styleId = `xeditor-styles-${this.containerId}`;
        if (document.getElementById(styleId)) return;

        const style = document.createElement("style");
        style.id = styleId;
        style.textContent = `
            .xeditor-wrapper {
                border: 1px solid #ddd;
                border-radius: 8px;
                overflow: hidden;
                background: white;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }

            .xeditor-fullscreen {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                z-index: 10000 !important;
                border-radius: 0 !important;
            }

            .xeditor-toolbar {
                background: #f8f9fa;
                border-bottom: 1px solid #e9ecef;
            }

            .xeditor-content {
                min-height: 300px;
                max-height: 600px;
                overflow-y: auto;
            }

            .xeditor-fullscreen .xeditor-content {
                max-height: calc(100vh - 120px);
            }

            .xeditor-editor {
                height: 100%;
            }

            .xeditor-status-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 16px;
                background: #f8f9fa;
                border-top: 1px solid #e9ecef;
                font-size: 12px;
                color: #6c757d;
            }

            .xeditor-stats {
                display: flex;
                gap: 16px;
            }

            .xeditor-actions {
                display: flex;
                gap: 8px;
            }

            .xeditor-actions button {
                background: none;
                border: 1px solid #dee2e6;
                padding: 4px 8px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 12px;
            }

            .xeditor-actions button:hover {
                background: #e9ecef;
            }

            .xeditor-dialog {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 10001;
            }

            .xeditor-dialog-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .xeditor-dialog-content {
                background: white;
                border-radius: 8px;
                max-width: 600px;
                max-height: 80vh;
                overflow-y: auto;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            }

            .xeditor-dialog-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 16px 20px;
                border-bottom: 1px solid #e9ecef;
            }

            .xeditor-dialog-header h3 {
                margin: 0;
                color: #333;
            }

            .xeditor-dialog-close {
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #999;
            }

            .xeditor-dialog-body {
                padding: 20px;
            }

            .template-list {
                display: grid;
                gap: 16px;
            }

            .template-item {
                border: 1px solid #e9ecef;
                border-radius: 6px;
                padding: 16px;
            }

            .template-item h4 {
                margin: 0 0 8px 0;
                color: #333;
            }

            .template-item p {
                margin: 0 0 12px 0;
                color: #666;
                font-size: 13px;
            }

            .template-item button {
                background: #007bff;
                color: white;
                border: none;
                padding: 6px 12px;
                border-radius: 4px;
                cursor: pointer;
            }

            .export-options {
                display: grid;
                gap: 16px;
            }

            .export-format {
                display: grid;
                gap: 8px;
            }

            .export-format label {
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
            }

            .export-options-advanced {
                display: grid;
                gap: 8px;
                padding: 12px;
                background: #f8f9fa;
                border-radius: 4px;
            }

            .export-options-advanced label {
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
            }

            .export-actions {
                display: flex;
                gap: 12px;
                justify-content: flex-end;
                margin-top: 20px;
            }

            .export-actions button {
                padding: 8px 16px;
                border-radius: 4px;
                cursor: pointer;
                font-weight: 500;
            }

            .export-actions button:first-child {
                background: #28a745;
                color: white;
                border: none;
            }

            .export-actions button:last-child {
                background: #6c757d;
                color: white;
                border: none;
            }

            .export-actions button:hover {
                opacity: 0.9;
            }

            /* Styles pour les tableaux */
            .xeditor-table {
                border-collapse: collapse;
                width: 100%;
                margin: 16px 0;
            }

            .xeditor-table td, .xeditor-table th {
                border: 1px solid #ddd;
                padding: 8px 12px;
                text-align: left;
            }

            .xeditor-table th {
                background-color: #f8f9fa;
                font-weight: 600;
            }

            .xeditor-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .xeditor-table tr:hover {
                background-color: #f5f5f5;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .xeditor-dialog-content {
                    margin: 20px;
                    max-width: calc(100vw - 40px);
                }

                .xeditor-status-bar {
                    flex-direction: column;
                    gap: 8px;
                    align-items: flex-start;
                }

                .xeditor-stats {
                    order: 2;
                }

                .xeditor-actions {
                    order: 1;
                    align-self: flex-end;
                }
            }

            /* Animation pour le plein écran */
            .xeditor-wrapper {
                transition: all 0.3s ease;
            }

            .ql-tooltip {
                z-index: 10002;
            }
        `;

        document.head.appendChild(style);
    }

    // ======================== MÉTHODES PUBLIQUES AVANCÉES ========================

    // Sauvegarde et chargement
    save(key = null) {
        const storageKey = key || `xeditor_${this.containerId}`;
        const content = {
            html: this.getContent("html"),
            delta: this.getContent("delta"),
            text: this.getContent("text"),
            json: this.getContent("json"),
            timestamp: Date.now(),
            version: "1.0.0",
        };

        localStorage.setItem(storageKey, JSON.stringify(content));
        this.emit("save", { key: storageKey, content });
        

        if (typeof this.config.saveCallback === "function") {
            this.config.saveCallback(content);
        } else {
            // Feedback visuel
            this.showNotification("Document sauvegardé", "success");
        }

        return storageKey;
    }

    load(key = null) {
        const storageKey = key || `xeditor_${this.containerId}`;
        const saved = localStorage.getItem(storageKey);

        if (saved) {
            try {
                const content = JSON.parse(saved);
                this.setContent(content.delta, "delta");
                this.emit("load", { key: storageKey, content });
                this.showNotification("Document chargé", "success");
                return true;
            } catch (e) {
                console.error("Erreur lors du chargement:", e);
                this.showNotification("Erreur lors du chargement", "error");
            }
        }
        return false;
    }

    // Gestion des images avancée
    handleImageDrop(e) {
        e.preventDefault();
        const files = Array.from(e.dataTransfer.files);
        const imageFiles = files.filter((file) =>
            file.type.startsWith("image/")
        );

        imageFiles.forEach((file) => {
            this.uploadImage(file);
        });
    }

    setupImageDragDrop() {
        const editor = this.quill.root;

        editor.addEventListener("dragover", (e) => {
            e.preventDefault();
            editor.classList.add("drag-over");
        });

        editor.addEventListener("dragleave", (e) => {
            e.preventDefault();
            editor.classList.remove("drag-over");
        });

        editor.addEventListener("drop", (e) => {
            editor.classList.remove("drag-over");
            this.handleImageDrop(e);
        });
    }

    // Recherche et remplacement
    find(text, options = {}) {
        const config = {
            caseSensitive: false,
            wholeWord: false,
            regex: false,
            ...options,
        };

        const content = this.getContent("text");
        let searchText = text;

        if (!config.caseSensitive) {
            searchText = searchText.toLowerCase();
        }

        if (config.regex) {
            try {
                const flags = config.caseSensitive ? "g" : "gi";
                const regex = new RegExp(searchText, flags);
                const matches = Array.from(content.matchAll(regex));
                return matches.map((match) => ({
                    index: match.index,
                    length: match[0].length,
                    text: match[0],
                }));
            } catch (e) {
                console.error("Regex invalide:", e);
                return [];
            }
        }

        const results = [];
        const searchIn = config.caseSensitive ? content : content.toLowerCase();
        let index = 0;

        while ((index = searchIn.indexOf(searchText, index)) !== -1) {
            if (config.wholeWord) {
                const before = index > 0 ? content[index - 1] : " ";
                const after =
                    index + searchText.length < content.length
                        ? content[index + searchText.length]
                        : " ";

                if (!/\w/.test(before) && !/\w/.test(after)) {
                    results.push({
                        index,
                        length: searchText.length,
                        text: content.substr(index, searchText.length),
                    });
                }
            } else {
                results.push({
                    index,
                    length: searchText.length,
                    text: content.substr(index, searchText.length),
                });
            }
            index += searchText.length;
        }

        return results;
    }

    replace(searchText, replaceText, options = {}) {
        const results = this.find(searchText, options);
        let offset = 0;

        results.forEach((result) => {
            const adjustedIndex = result.index + offset;
            this.quill.deleteText(adjustedIndex, result.length);
            this.quill.insertText(adjustedIndex, replaceText);
            offset += replaceText.length - result.length;
        });

        return results.length;
    }

    // Statistiques avancées
    getStatistics() {
        const text = this.getContent("text");
        const html = this.getContent("html");

        const stats = {
            characters: text.length,
            charactersNoSpaces: text.replace(/\s/g, "").length,
            words: text.trim() ? text.trim().split(/\s+/).length : 0,
            paragraphs: text.split(/\n\s*\n/).length,
            sentences: text.split(/[.!?]+/).filter((s) => s.trim().length > 0)
                .length,
            averageWordsPerSentence: 0,
            readingTime: 0, // en minutes
            images: (html.match(/<img/g) || []).length,
            links: (html.match(/<a/g) || []).length,
            tables: (html.match(/<table/g) || []).length,
        };

        if (stats.sentences > 0) {
            stats.averageWordsPerSentence = Math.round(
                stats.words / stats.sentences
            );
        }

        // Temps de lecture approximatif (250 mots/minute)
        stats.readingTime = Math.ceil(stats.words / 250);

        return stats;
    }

    showStatistics() {
        const stats = this.getStatistics();
        const content = document.createElement("div");
        content.className = "statistics-panel";
        content.innerHTML = `
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-label">Mots:</span>
                    <span class="stat-value">${stats.words}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Caractères:</span>
                    <span class="stat-value">${stats.characters}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Sans espaces:</span>
                    <span class="stat-value">${stats.charactersNoSpaces}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Paragraphes:</span>
                    <span class="stat-value">${stats.paragraphs}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Phrases:</span>
                    <span class="stat-value">${stats.sentences}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Mots/phrase (moy.):</span>
                    <span class="stat-value">${stats.averageWordsPerSentence}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Temps de lecture:</span>
                    <span class="stat-value">${stats.readingTime} min</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Images:</span>
                    <span class="stat-value">${stats.images}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Liens:</span>
                    <span class="stat-value">${stats.links}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Tableaux:</span>
                    <span class="stat-value">${stats.tables}</span>
                </div>
            </div>
        `;

        const dialog = this.createDialog("Statistiques du document", content);
        document.body.appendChild(dialog);
    }

    // Notification système
    showNotification(message, type = "info", duration = 3000) {
        const notification = document.createElement("div");
        notification.className = `xeditor-notification xeditor-notification-${type}`;
        notification.textContent = message;

        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: ${
                type === "success"
                    ? "#28a745"
                    : type === "error"
                    ? "#dc3545"
                    : "#007bff"
            };
            color: white;
            border-radius: 4px;
            z-index: 10003;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;

        document.body.appendChild(notification);

        // Animation d'entrée
        setTimeout(() => {
            notification.style.transform = "translateX(0)";
        }, 100);

        // Suppression automatique
        setTimeout(() => {
            notification.style.transform = "translateX(100%)";
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, duration);
    }

    // Collaboration (basique)
    enableCollaboration(config = {}) {
        // Configuration de base pour la collaboration
        this.collaborationConfig = {
            endpoint: config.endpoint || "/api/collaboration",
            updateInterval: config.updateInterval || 5000,
            userId: config.userId || "anonymous",
            ...config,
        };

        // Envoyer les changements
        this.quill.on("text-change", (delta, oldDelta, source) => {
            if (source === "user") {
                this.sendCollaborationUpdate(delta);
            }
        });

        // Polling pour récupérer les changements
        if (this.collaborationConfig.updateInterval > 0) {
            this.collaborationInterval = setInterval(() => {
                this.fetchCollaborationUpdates();
            }, this.collaborationConfig.updateInterval);
        }
    }

    sendCollaborationUpdate(delta) {
        if (!this.collaborationConfig) return;

        // Simulé - à remplacer par un vrai appel API
        console.log("Envoi de mise à jour collaborative:", delta);

        // Exemple d'implémentation
        // fetch(this.collaborationConfig.endpoint, {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({
        //         delta,
        //         userId: this.collaborationConfig.userId,
        //         timestamp: Date.now()
        //     })
        // });
    }

    fetchCollaborationUpdates() {
        if (!this.collaborationConfig) return;

        // Simulé - à remplacer par un vrai appel API
        console.log("Récupération des mises à jour collaboratives");

        // Exemple d'implémentation
        // fetch(`${this.collaborationConfig.endpoint}?since=${this.lastUpdateTimestamp}`)
        //     .then(response => response.json())
        //     .then(updates => {
        //         updates.forEach(update => {
        //             if (update.userId !== this.collaborationConfig.userId) {
        //                 this.quill.updateContents(update.delta, 'api');
        //             }
        //         });
        //     });
    }

    disableCollaboration() {
        if (this.collaborationInterval) {
            clearInterval(this.collaborationInterval);
            this.collaborationInterval = null;
        }
        this.collaborationConfig = null;
    }

    // Prévisualisation
    preview() {
        const content = this.getContent("html");
        const previewWindow = window.open("", "_blank", "width=800,height=600");

        previewWindow.document.write(`
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aperçu - XEditor</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            color: #333;
        }
        ${this.getEditorStyles()}
    </style>
</head>
<body>
    <div class="ql-editor">
        ${content}
    </div>
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.close();
            }
        });
    </script>
</body>
</html>`);

        previewWindow.document.close();
        previewWindow.focus();
    }

    // Nettoyage
    destroy() {
        // Arrêter les timers
        if (this.autosaveTimer) {
            clearInterval(this.autosaveTimer);
        }

        if (this.collaborationInterval) {
            clearInterval(this.collaborationInterval);
        }

        // Détruire Quill
        if (this.quill) {
            this.quill.off("text-change");
            this.quill.off("selection-change");
        }

        // Nettoyer les plugins
        this.plugins.forEach((plugin, name) => {
            this.unregisterPlugin(name);
        });

        // Supprimer les styles
        const styleElement = document.getElementById(
            `xeditor-styles-${this.containerId}`
        );
        if (styleElement) {
            styleElement.remove();
        }

        // Nettoyer les événements
        this._events = {};

        // Restaurer le body overflow si en plein écran
        if (this.container.classList.contains("xeditor-fullscreen")) {
            document.body.style.overflow = "";
        }

        console.log("XEditor détruit");
    }

    // ======================== MÉTHODES STATIQUES ========================

    static createFromTextarea(textareaId, options = {}) {
        const textarea = document.getElementById(textareaId);
        if (!textarea) {
            throw new Error(`Textarea #${textareaId} non trouvée`);
        }

        const container = document.createElement("div");
        container.id = `${textareaId}-xeditor`;
        textarea.parentNode.insertBefore(container, textarea);

        const editor = new XEditor(container.id, {
            ...options,
            placeholder: options.placeholder || textarea.placeholder,
        });

        // Synchroniser avec le textarea
        if (textarea.value) {
            editor.setContent(textarea.value, "text");
        }

        editor.on("text-change", () => {
            textarea.value = editor.getContent("html");
        });

        textarea.style.display = "none";

        return editor;
    }

    static getVersion() {
        return "1.0.0";
    }

    static checkDependencies() {
        const dependencies = {
            Quill: typeof Quill !== "undefined",
            html2pdf: typeof html2pdf !== "undefined",
            katex: typeof katex !== "undefined",
        };

        console.log("XEditor - Dépendances:", dependencies);
        return dependencies;
    }
}

// ======================== EXPORT ET INITIALISATION GLOBALE ========================

// Instance globale pour compatibilité
window.xeditorInstance = null;

// Export
if (typeof window !== "undefined") {
    window.XEditor = XEditor;
}

if (typeof module !== "undefined" && module.exports) {
    module.exports = XEditor;
}

// Fonction d'initialisation rapide
window.initXEditor = function (containerId, options = {}) {
    window.xeditorInstance = new XEditor(containerId, options);
    return window.xeditorInstance;
};

// Templates prédéfinis
XEditor.TEMPLATES = {
    BUSINESS_LETTER: {
        name: "Lettre commerciale",
        content: `
            <p><strong>[Votre nom]</strong><br>
            [Votre adresse]<br>
            [Ville, Code postal]<br>
            [Email]<br>
            [Téléphone]</p>
            
            <p style="margin-top: 40px;">[Date]</p>
            
            <p><strong>[Nom du destinataire]</strong><br>
            [Titre/Poste]<br>
            [Société]<br>
            [Adresse]</p>
            
            <p><strong>Objet:</strong> [Objet de la lettre]</p>
            
            <p>Madame, Monsieur,</p>
            
            <p>[Contenu de votre lettre...]</p>
            
            <p>Je vous prie d'agréer, Madame, Monsieur, l'expression de mes salutations distinguées.</p>
            
            <p><strong>[Votre nom]</strong><br>
            [Votre signature]</p>
        `,
    },
    REPORT: {
        name: "Rapport",
        content: `
            <h1 style="text-align: center;">TITRE DU RAPPORT</h1>
            
            <p style="text-align: center;"><em>Sous-titre ou description</em></p>
            
            <hr>
            
            <h2>1. RÉSUMÉ EXÉCUTIF</h2>
            <p>[Résumé du rapport...]</p>
            
            <h2>2. INTRODUCTION</h2>
            <p>[Introduction et contexte...]</p>
            
            <h2>3. MÉTHODOLOGIE</h2>
            <p>[Description de la méthodologie utilisée...]</p>
            
            <h2>4. RÉSULTATS</h2>
            <p>[Présentation des résultats...]</p>
            
            <h2>5. ANALYSE</h2>
            <p>[Analyse des résultats...]</p>
            
            <h2>6. RECOMMANDATIONS</h2>
            <ul>
                <li>[Recommandation 1]</li>
                <li>[Recommandation 2]</li>
                <li>[Recommandation 3]</li>
            </ul>
            
            <h2>7. CONCLUSION</h2>
            <p>[Conclusion du rapport...]</p>
        `,
    },
    ARTICLE: {
        name: "Article de blog",
        content: `
            <h1>[Titre accrocheur de votre article]</h1>
            
            <p><em>Publié le [Date] par [Auteur]</em></p>
            
            <p><strong>Introduction:</strong> [Une introduction captivante qui présente le sujet et accroche le lecteur...]</p>
            
            <h2>Sous-titre principal</h2>
            <p>[Développement du premier point...]</p>
            
            <blockquote>
                <p>"[Une citation pertinente ou un point important à mettre en évidence]"</p>
            </blockquote>
            
            <h2>Autre sous-titre</h2>
            <p>[Développement du deuxième point...]</p>
            
            <ul>
                <li>Point important 1</li>
                <li>Point important 2</li>
                <li>Point important 3</li>
            </ul>
            
            <h2>Conclusion</h2>
            <p>[Résumé des points clés et appel à l'action...]</p>
            
            <hr>
            
            <p><em>Tags: [tag1, tag2, tag3]</em></p>
        `,
    },
};

console.log("📝 XEditor v1.0.0 chargé - Prêt à l'utilisation!");

// <!-- Dépendances requises -->
// <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
// <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

// <!-- Optionnel pour PDF -->
// <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

// <!-- Optionnel pour les maths -->
// <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.13.11/dist/katex.min.css">
// <script src="https://cdn.jsdelivr.net/npm/katex@0.13.11/dist/katex.min.js"></script>

// <!-- XEditor -->
// <script src="xeditor.js"></script>
