class FileManager {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.multiple = options.multiple || false;
        this.fieldName = options.fieldName || (this.multiple ? 'files[]' : 'file');
        this.files = [];
        this.init();
    }

    init() {
        this.container.innerHTML = `
            <div class="file-manager">
                <div class="d-flex align-items-center mb-3">
                    <button type="button" class="btn btn-outline-primary" id="addFileBtn-${this.container.id}">
                        <i class="bi bi-plus-circle"></i> Ajouter un fichier
                    </button>
                    <input type="file" class="d-none" id="fileInput-${this.container.id}" ${this.multiple ? 'multiple' : ''}>
                </div>
                <div class="file-list" id="fileList-${this.container.id}">
                    ${this.files.length === 0 ? '<p class="text-muted">Aucun fichier sélectionné</p>' : ''}
                </div>
                ${this.generateFileInputs()}
            </div>
        `;

        this.bindEvents();
    }

    bindEvents() {
        const addBtn = document.getElementById(`addFileBtn-${this.container.id}`);
        const fileInput = document.getElementById(`fileInput-${this.container.id}`);

        addBtn.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', (e) => {
            this.handleFileSelection(e.target.files);
            e.target.value = ''; // Reset input
        });
    }

    handleFileSelection(selectedFiles) {
        if (this.multiple) {
            Array.from(selectedFiles).forEach(file => {
                if (!this.files.find(f => f.name === file.name && f.size === file.size)) {
                    this.files.push(file);
                }
            });
        } else {
            this.files = selectedFiles.length > 0 ? [selectedFiles[0]] : [];
        }
        this.render();
    }

    removeFile(index) {
        this.files.splice(index, 1);
        this.render();
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    getFileIcon(fileName) {
        const ext = fileName.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': 'bi-file-earmark-pdf text-danger',
            'doc': 'bi-file-earmark-word text-primary',
            'docx': 'bi-file-earmark-word text-primary',
            'xls': 'bi-file-earmark-excel text-success',
            'xlsx': 'bi-file-earmark-excel text-success',
            'ppt': 'bi-file-earmark-ppt text-warning',
            'pptx': 'bi-file-earmark-ppt text-warning',
            'jpg': 'bi-file-earmark-image text-info',
            'jpeg': 'bi-file-earmark-image text-info',
            'png': 'bi-file-earmark-image text-info',
            'gif': 'bi-file-earmark-image text-info',
            'txt': 'bi-file-earmark-text text-secondary',
            'zip': 'bi-file-earmark-zip text-warning',
            'rar': 'bi-file-earmark-zip text-warning'
        };
        return iconMap[ext] || 'bi-file-earmark text-muted';
    }

    generateFileInputs() {
        if (this.files.length === 0) return '';
        
        if (this.multiple) {
            return this.files.map((file, index) => 
                `<input type="file" name="${this.fieldName}" class="d-none file-input" data-index="${index}">`
            ).join('');
        } else {
            return `<input type="file" name="${this.fieldName}" class="d-none file-input">`;
        }
    }

    updateFileInputs() {
        const fileInputs = this.container.querySelectorAll('.file-input');
        
        fileInputs.forEach((input, index) => {
            if (this.multiple) {
                // Créer un DataTransfer pour stocker le fichier
                const dataTransfer = new DataTransfer();
                if (this.files[index]) {
                    dataTransfer.items.add(this.files[index]);
                    input.files = dataTransfer.files;
                }
            } else {
                const dataTransfer = new DataTransfer();
                if (this.files[0]) {
                    dataTransfer.items.add(this.files[0]);
                    input.files = dataTransfer.files;
                }
            }
        });
    }

    render() {
        const fileList = document.getElementById(`fileList-${this.container.id}`);
        
        if (this.files.length === 0) {
            fileList.innerHTML = '<p class="text-muted mb-0">Aucun fichier sélectionné</p>';
        } else {
            fileList.innerHTML = this.files.map((file, index) => `
                <div class="card mb-2">
                    <div class="card-body py-2">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <i class="${this.getFileIcon(file.name)} fs-4"></i>
                            </div>
                            <div class="col">
                                <div class="fw-semibold text-truncate">${file.name}</div>
                                <small class="text-muted">${this.formatFileSize(file.size)}</small>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="fileManager${this.container.id}.removeFile(${index})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Mettre à jour les inputs file cachés
        const fileManagerContainer = this.container.querySelector('.file-manager');
        const existingInputs = fileManagerContainer.querySelectorAll('.file-input');
        existingInputs.forEach(input => input.remove());
        
        if (this.files.length > 0) {
            const fileInputsHTML = this.generateFileInputs();
            fileManagerContainer.insertAdjacentHTML('beforeend', fileInputsHTML);
            this.updateFileInputs();
        }
    }

    // Méthodes publiques pour récupérer les données
    getFiles() {
        return this.files;
    }

    getFileNames() {
        return this.files.map(file => file.name);
    }

    clear() {
        this.files = [];
        this.render();
    }
}

// Fonction d'initialisation
function createFileManager(containerId, options = {}) {
    const manager = new FileManager(containerId, options);
    // Stocker l'instance globalement pour les callbacks
    window[`fileManager${containerId}`] = manager;
    return manager;
}