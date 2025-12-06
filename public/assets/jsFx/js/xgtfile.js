/**
 * Classe universelle pour gérer les galeries d'images, les fichiers et les tableaux
 * Utilise Bootstrap pour le style
 */
class XGtFile {
    constructor(data, mapping, endpoints = {}, options = {}) {
        this.data = Array.isArray(data) ? data : Object.values(data);
        this.mapping = mapping;
        this.endpoints = endpoints;
        this.options = {
            container: options.container || 'body',
            mode: options.mode || 'image', // 'image' ou 'file'
            beforeCall: options.beforeCall || null,
            afterCall: options.afterCall || null,
            confirmMessage: options.confirmMessage || 'Êtes-vous sûr de vouloir effectuer cette action ?',
            imageWidth: options.imageWidth || null,
            imageHeight: options.imageHeight || 200,
            aspectRatio: options.aspectRatio || null,
            showPagination: options.showPagination || false,
            itemsPerPage: options.itemsPerPage || 10,
            searchable: options.searchable || false,
            fileIconSize: options.fileIconSize || 64,
            showFileInfo: options.showFileInfo !== false, // par défaut true
            defineAction: options.defineAction || [],
            ...options
        };
        
        this.currentPage = 1;
        this.filteredData = [...this.data];
        this.container = null;
        this.type = null; // 'gallery' ou 'table'
        
        // Extensions de fichiers et leurs icônes
        this.fileIcons = {
            pdf: 'fas fa-file-pdf text-danger',
            doc: 'fas fa-file-word text-primary',
            docx: 'fas fa-file-word text-primary',
            xls: 'fas fa-file-excel text-success',
            xlsx: 'fas fa-file-excel text-success',
            ppt: 'fas fa-file-powerpoint text-warning',
            pptx: 'fas fa-file-powerpoint text-warning',
            txt: 'fas fa-file-alt text-secondary',
            zip: 'fas fa-file-archive text-info',
            rar: 'fas fa-file-archive text-info',
            mp4: 'fas fa-file-video text-purple',
            avi: 'fas fa-file-video text-purple',
            mp3: 'fas fa-file-audio text-success',
            wav: 'fas fa-file-audio text-success',
            jpg: 'fas fa-file-image text-info',
            jpeg: 'fas fa-file-image text-info',
            png: 'fas fa-file-image text-info',
            gif: 'fas fa-file-image text-info',
            svg: 'fas fa-file-image text-info',
            default: 'fas fa-file text-secondary'
        };
        
        this.init();
    }

    init() {
        this.container = typeof this.options.container === 'string' 
            ? document.querySelector(this.options.container) 
            : this.options.container;
            
        if (!this.container) {
            console.error('Conteneur non trouvé');
            return;
        }
    }

    /**
     * Créer une galerie d'images ou de fichiers
     */
    gallery() {
        this.type = 'gallery';
        this.render();
        return this;
    }

    /**
     * Créer un tableau
     */
    table() {
        this.type = 'table';
        this.render();
        return this;
    }

    render() {
        this.container.innerHTML = '';
        
        // Créer le wrapper principal
        const wrapper = document.createElement('div');
        wrapper.className = 'data-manager-wrapper';
        
        // Ajouter la recherche si activée
        if (this.options.searchable) {
            wrapper.appendChild(this.createSearchBox());
        }
        
        // Créer le contenu principal
        if (this.type === 'gallery') {
            wrapper.appendChild(this.createGallery());
        } else if (this.type === 'table') {
            wrapper.appendChild(this.createTable());
        }
        
        // Ajouter la pagination si activée
        if (this.options.showPagination) {
            wrapper.appendChild(this.createPagination());
        }
        
        this.container.appendChild(wrapper);
        
        // Créer le modal de confirmation
        if (!document.getElementById('confirmModal')) {
            this.createModal();
        }
    }

    createSearchBox() {
        const searchContainer = document.createElement('div');
        searchContainer.className = 'mb-3';
        
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'form-control';
        searchInput.placeholder = 'Rechercher...';
        
        searchInput.addEventListener('input', (e) => {
            this.search(e.target.value);
        });
        
        searchContainer.appendChild(searchInput);
        return searchContainer;
    }

    createGallery() {
        const gallery = document.createElement('div');
        gallery.className = 'row g-3';
        
        const paginatedData = this.getPaginatedData();
        
        paginatedData.forEach((item, index) => {
            const col = document.createElement('div');
            col.className = 'col-12 col-sm-6 col-md-4 col-lg-3';
            
            const card = document.createElement('div');
            card.className = `card ${this.options.mode}-item h-100`;
            card.dataset.id = item[this.mapping.id];
            
            if (this.options.mode === 'image') {
                card.appendChild(this.createImageCard(item));
            } else {
                card.appendChild(this.createFileCard(item));
            }
            
            col.appendChild(card);
            gallery.appendChild(col);
        });
        
        return gallery;
    }

    createImageCard(item) {
        const cardContent = document.createElement('div');
        const imageUrl = item[this.mapping.url];
        const imageStyle = this.getImageStyle();
        
        cardContent.innerHTML = `
            <div class="position-relative">
                <img src="${imageUrl}" 
                     class="card-img-top gallery-image" 
                     style="${imageStyle}"
                     alt="Image ${item[this.mapping.id]}"
                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlIG5vbiBkaXNwb25pYmxlPC90ZXh0Pjwvc3ZnPg=='">
                ${this.createActionButtons(item).outerHTML}
            </div>
            ${item[this.mapping.name] ? `<div class="card-body p-2"><h6 class="card-title text-truncate mb-0" title="${item[this.mapping.name]}">${item[this.mapping.name]}</h6></div>` : ''}
        `;
        
        return cardContent;
    }

    createFileCard(item) {
        const cardContent = document.createElement('div');
        const fileName = item[this.mapping.name] || 'Fichier sans nom';
        const fileExtension = this.getFileExtension(fileName);
        const fileIcon = this.getFileIcon(fileExtension);
        const fileSize = this.formatFileSize(item[this.mapping.size]);
        
        cardContent.className = 'position-relative h-100 d-flex flex-column';
        
        cardContent.innerHTML = `
            <div class="card-body text-center d-flex flex-column justify-content-center flex-grow-1 p-3">
                <div class="mb-3">
                    <i class="${fileIcon}" style="font-size: ${this.options.fileIconSize}px;"></i>
                </div>
                <h6 class="card-title text-truncate mb-2" title="${fileName}">${fileName}</h6>
                ${this.options.showFileInfo ? `
                    <div class="file-info text-muted small">
                        <div class="mb-1"><span class="badge bg-secondary">${fileExtension.toUpperCase()}</span></div>
                        ${fileSize ? `<div class="text-xs">${fileSize}</div>` : ''}
                        ${item[this.mapping.date] ? `<div class="text-xs">${this.formatDate(item[this.mapping.date])}</div>` : ''}
                    </div>
                ` : ''}
            </div>
            ${this.createActionButtons(item, false).outerHTML}
        `;
        
        // Positionner les boutons d'action en overlay
        const actionButtons = cardContent.querySelector('.d-flex.gap-1');
        if (actionButtons) {
            actionButtons.className = 'position-absolute top-0 end-0 m-2 d-flex gap-1';
        }
        
        return cardContent;
    }

    createTable() {
        const tableContainer = document.createElement('div');
        tableContainer.className = 'table-responsive';
        
        const table = document.createElement('table');
        table.className = 'table table-striped table-hover';
        
        // En-têtes
        const thead = document.createElement('thead');
        thead.className = 'table-dark';
        const headerRow = document.createElement('tr');
        
        Object.keys(this.mapping).forEach(key => {
            if (key !== 'actions') {
                const th = document.createElement('th');
                th.textContent = this.getHeaderLabel(key);
                headerRow.appendChild(th);
            }
        });
        
        // Colonne actions si nécessaire
        if (this.hasActions() || this.options.defineAction.length > 0) {
            const th = document.createElement('th');
            th.textContent = 'Actions';
            th.className = 'text-center';
            headerRow.appendChild(th);
        }
        
        thead.appendChild(headerRow);
        table.appendChild(thead);
        
        // Corps du tableau
        const tbody = document.createElement('tbody');
        const paginatedData = this.getPaginatedData();
        
        paginatedData.forEach(item => {
            const row = document.createElement('tr');
            row.dataset.id = item[this.mapping.id];
            
            Object.keys(this.mapping).forEach(key => {
                if (key !== 'actions') {
                    const td = document.createElement('td');
                    const value = item[this.mapping[key]];
                    
                    if (key === 'url' && value) {
                        if (this.options.mode === 'image') {
                            td.innerHTML = `<img src="${value}" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">`;
                        } else {
                            td.innerHTML = `<a href="${value}" target="_blank" class="text-decoration-none">
                                <i class="fas fa-external-link-alt me-1"></i>Voir
                            </a>`;
                        }
                    } else if (key === 'name' && this.options.mode === 'file') {
                        const extension = this.getFileExtension(value);
                        const icon = this.getFileIcon(extension);
                        td.innerHTML = `<i class="${icon} me-2"></i>${value || ''}`;
                    } else if (key === 'size') {
                        td.textContent = this.formatFileSize(value);
                    } else if (key === 'date') {
                        td.textContent = this.formatDate(value);
                    } else {
                        td.textContent = value || '';
                    }
                    
                    row.appendChild(td);
                }
            });
            
            // Colonne actions
            if (this.hasActions()) {
                const td = document.createElement('td');
                td.className = 'text-center';
                td.appendChild(this.createActionButtons(item, false));
                row.appendChild(td);
            }
            
            tbody.appendChild(row);
        });
        
        table.appendChild(tbody);
        tableContainer.appendChild(table);
        
        return tableContainer;
    }

    createActionButtons(item, isOverlay = true) {
        const buttonContainer = document.createElement('div');
        buttonContainer.className = isOverlay 
            ? 'position-absolute top-0 end-0 m-2 d-flex gap-1' 
            : 'd-flex gap-1 justify-content-center';
        
        // Bouton de visualisation (pour les fichiers)
        if (this.options.mode === 'file' && this.endpoints.view) {
            const viewBtn = document.createElement('button');
            viewBtn.type = 'button';
            viewBtn.className = 'btn btn-primary btn-sm';
            viewBtn.innerHTML = '<i class="fas fa-eye"></i>';
            viewBtn.title = 'Visualiser';
            viewBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.viewItem(item[this.mapping.id]);
            });
            buttonContainer.appendChild(viewBtn);
        }
        
        // Bouton de téléchargement
        if (this.endpoints.download) {
            const downloadBtn = document.createElement('button');
            downloadBtn.type = 'button';
            downloadBtn.className = 'btn btn-success btn-sm';
            downloadBtn.innerHTML = '<i class="fas fa-download"></i>';
            downloadBtn.title = 'Télécharger';
            downloadBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.downloadItem(item[this.mapping.id], item[this.mapping.name]);
            });
            buttonContainer.appendChild(downloadBtn);
        }
        
        // Bouton d'impression
        if (this.endpoints.print) {
            const printBtn = document.createElement('button');
            printBtn.type = 'button';
            printBtn.className = 'btn btn-info btn-sm';
            printBtn.innerHTML = '<i class="fas fa-print"></i>';
            printBtn.title = 'Imprimer';
            printBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.printItem(item[this.mapping.id]);
            });
            buttonContainer.appendChild(printBtn);
        }
        
        // Bouton de suppression
        if (this.endpoints.delete) {
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'btn btn-danger btn-sm';
            deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
            deleteBtn.title = 'Supprimer';
            deleteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.showDeleteModal(item[this.mapping.id]);
            });
            buttonContainer.appendChild(deleteBtn);
        }

        if (this.options.defineAction.length > 0) {
            this.options.defineAction.forEach(action => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-' + action.color + ' btn-sm';
                btn.innerHTML = '<i class="' + action.icon + '"></i>';
                btn.title = action.title;
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    action.action(item[this.mapping.id]);
                });
                buttonContainer.appendChild(btn);
            });
            
        }
        
        return buttonContainer;
    }

    createPagination() {
        const totalPages = Math.ceil(this.filteredData.length / this.options.itemsPerPage);
        
        if (totalPages <= 1) return document.createElement('div');
        
        const nav = document.createElement('nav');
        nav.className = 'mt-3';
        
        const ul = document.createElement('ul');
        ul.className = 'pagination justify-content-center';
        
        // Bouton précédent
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${this.currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#">Précédent</a>`;
        prevLi.addEventListener('click', (e) => {
            e.preventDefault();
            if (this.currentPage > 1) {
                this.currentPage--;
                this.render();
            }
        });
        ul.appendChild(prevLi);
        
        // Numéros de pages
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === this.currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener('click', (e) => {
                e.preventDefault();
                this.currentPage = i;
                this.render();
            });
            ul.appendChild(li);
        }
        
        // Bouton suivant
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${this.currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#">Suivant</a>`;
        nextLi.addEventListener('click', (e) => {
            e.preventDefault();
            if (this.currentPage < totalPages) {
                this.currentPage++;
                this.render();
            }
        });
        ul.appendChild(nextLi);
        
        nav.appendChild(ul);
        return nav;
    }

    createModal() {
        const modal = document.createElement('div');
        modal.id = 'confirmModal';
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p id="modalMessage">${this.options.confirmMessage}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-danger" id="confirmAction">Confirmer</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
    }

    // Méthodes utilitaires
    getImageStyle() {
        let style = '';
        
        if (this.options.imageWidth) {
            style += `width: ${this.options.imageWidth}px; `;
        }
        
        if (this.options.imageHeight) {
            style += `height: ${this.options.imageHeight}px; `;
        }
        
        if (this.options.aspectRatio) {
            style += `aspect-ratio: ${this.options.aspectRatio}; `;
        }
        
        return style + 'object-fit: cover;';
    }

    getFileExtension(filename) {
        if (!filename) return '';
        const parts = filename.split('.');
        return parts.length > 1 ? parts[parts.length - 1].toLowerCase() : '';
    }

    getFileIcon(extension) {
        return this.fileIcons[extension] || this.fileIcons.default;
    }

    formatFileSize(bytes) {
        if (!bytes || bytes === 0) return '';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
    }

    getHeaderLabel(key) {
        const labels = {
            id: 'ID',
            name: 'Nom',
            url: this.options.mode === 'image' ? 'Image' : '',
            size: 'Taille',
            date: 'Date',
            type: 'Type'
        };
        return labels[key] || key.charAt(0).toUpperCase() + key.slice(1);
    }

    hasActions() {
        return this.endpoints.delete || this.endpoints.download || this.endpoints.print || (this.options.mode === 'file' && this.endpoints.view);
    }

    getPaginatedData() {
        if (!this.options.showPagination) return this.filteredData;
        
        const start = (this.currentPage - 1) * this.options.itemsPerPage;
        const end = start + this.options.itemsPerPage;
        return this.filteredData.slice(start, end);
    }

    search(query) {
        if (!query) {
            this.filteredData = [...this.data];
        } else {
            this.filteredData = this.data.filter(item => {
                return Object.keys(this.mapping).some(key => {
                    const value = item[this.mapping[key]];
                    return value && value.toString().toLowerCase().includes(query.toLowerCase());
                });
            });
        }
        
        this.currentPage = 1;
        this.render();
    }

    // Actions
    async viewItem(itemId) {
        try {
            if (this.options.beforeCall) {
                this.options.beforeCall('view', itemId);
            }
            
            const url = this.getEndpointUrl('view', itemId);
            window.open(url, '_blank');
            
            if (this.options.afterCall) {
                this.options.afterCall('view', itemId, true);
            }
        } catch (error) {
            console.error('Erreur de visualisation:', error);
            if (this.options.afterCall) {
                this.options.afterCall('view', itemId, false, error);
            }
        }
    }

    async downloadItem(itemId, fileName) {
        try {
            if (this.options.beforeCall) {
                this.options.beforeCall('download', itemId);
            }
            
            const url = this.getEndpointUrl('download', itemId);
            const response = await fetch(url);
            
            if (response.ok) {
                const blob = await response.blob();
                const downloadUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                a.download = fileName || `file_${itemId}`;
                a.click();
                window.URL.revokeObjectURL(downloadUrl);
                
                if (this.options.afterCall) {
                    this.options.afterCall('download', itemId, true, response);
                }
            }
        } catch (error) {
            console.error('Erreur de téléchargement:', error);
            if (this.options.afterCall) {
                this.options.afterCall('download', itemId, false, error);
            }
        }
    }

    async printItem(itemId) {
        try {
            if (this.options.beforeCall) {
                this.options.beforeCall('print', itemId);
            }
            
            const url = this.getEndpointUrl('print', itemId);
            window.open(url, '_blank');
            
            if (this.options.afterCall) {
                this.options.afterCall('print', itemId, true);
            }
        } catch (error) {
            console.error('Erreur d\'impression:', error);
            if (this.options.afterCall) {
                this.options.afterCall('print', itemId, false, error);
            }
        }
    }

    showDeleteModal(itemId) {
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        const confirmBtn = document.getElementById('confirmAction');
        
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        newConfirmBtn.addEventListener('click', () => {
            modal.hide();
            this.deleteItem(itemId);
        });
        
        modal.show();
    }

    async deleteItem(itemId) {
        try {
            if (this.options.beforeCall) {
                this.options.beforeCall('delete', itemId);
            }
            
            const element = document.querySelector(`[data-id="${itemId}"]`);
            if (element) {
                element.classList.add('opacity-50');
            }
            
            const url = this.getEndpointUrl('delete', itemId);
            const response = await fetch(url, { method: 'DELETE' });
            
            if (response.ok) {
                // Supprimer de la data
                this.data = this.data.filter(item => item[this.mapping.id] != itemId);
                this.filteredData = this.filteredData.filter(item => item[this.mapping.id] != itemId);
                
                if (element) {
                    element.style.transform = 'scale(0)';
                    element.style.transition = 'all 0.3s ease';
                    setTimeout(() => element.remove(), 300);
                }
                
                if (this.options.afterCall) {
                    this.options.afterCall('delete', itemId, true, response);
                }
            } else {
                if (element) {
                    element.classList.remove('opacity-50');
                }
            }
        } catch (error) {
            console.error('Erreur de suppression:', error);
            const element = document.querySelector(`[data-id="${itemId}"]`);
            if (element) {
                element.classList.remove('opacity-50');
            }
            
            if (this.options.afterCall) {
                this.options.afterCall('delete', itemId, false, error);
            }
        }
    }

    getEndpointUrl(action, itemId) {
        const endpoint = this.endpoints[action];
        if (typeof endpoint === 'function') {
            return endpoint(itemId);
        }
        return endpoint.replace('{id}', itemId).replace('{i}', itemId);
    }

    // Méthodes publiques pour la manipulation
    addItem(item) {
        this.data.push(item);
        this.filteredData.push(item);
        this.render();
    }

    removeItem(itemId) {
        this.data = this.data.filter(item => item[this.mapping.id] != itemId);
        this.filteredData = this.filteredData.filter(item => item[this.mapping.id] != itemId);
        this.render();
    }

    updateItem(itemId, newData) {
        const index = this.data.findIndex(item => item[this.mapping.id] == itemId);
        if (index !== -1) {
            this.data[index] = { ...this.data[index], ...newData };
            const filteredIndex = this.filteredData.findIndex(item => item[this.mapping.id] == itemId);
            if (filteredIndex !== -1) {
                this.filteredData[filteredIndex] = this.data[index];
            }
            this.render();
        }
    }

    refresh() {
        this.render();
    }

    getData() {
        return this.data;
    }

    getFilteredData() {
        return this.filteredData;
    }

    // Méthode pour changer de mode
    setMode(mode) {
        this.options.mode = mode;
        this.render();
        return this;
    }
}

// Alias pour faciliter l'utilisation
class x_gt_ extends XGtFile {}

// Alias de méthodes courantes
x_gt_.prototype.g = x_gt_.prototype.gallery;
x_gt_.prototype.t = x_gt_.prototype.table;
x_gt_.prototype.add = x_gt_.prototype.addItem;
x_gt_.prototype.remove = x_gt_.prototype.removeItem;
x_gt_.prototype.update = x_gt_.prototype.updateItem;
x_gt_.prototype.r = x_gt_.prototype.refresh;

// Export pour utilisation
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { XGtFile, x_gt_ };
}

// Exemples d'utilisation :

/*
// ========== MODE IMAGE ==========
const imageData = [
    { id: 1, name: 'Image 1', url: 'https://picsum.photos/300/200?random=1' },
    { id: 2, name: 'Image 2', url: 'https://picsum.photos/300/200?random=2' }
];

const imageMapping = {
    id: 'id',
    name: 'name',
    url: 'url'
};

const imageManager = new XGtFile(imageData, imageMapping, {
    delete: 'https://api.example.com/images/{id}',
    download: (id) => `https://api.example.com/download/${id}`
}, {
    container: '#imageContainer',
    mode: 'image',
    imageHeight: 250,
    searchable: true
});

imageManager.gallery();

// ========== MODE FILE ==========
const fileData = [
    { 
        id: 1, 
        name: 'document.pdf', 
        url: '/files/document.pdf',
        size: 1024567,
        date: '2024-01-15'
    },
    { 
        id: 2, 
        name: 'presentation.pptx', 
        url: '/files/presentation.pptx',
        size: 2048000,
        date: '2024-01-20'
    }
];

const fileMapping = {
    id: 'id',
    name: 'name',
    url: 'url',
    size: 'size',
    date: 'date'
};

const fileManager = new XGtFile(fileData, fileMapping, {
    delete: 'https://api.example.com/files/{id}',
    download: 'https://api.example.com/download/{id}',
    view: 'https://api.example.com/view/{id}'
}, {
    container: '#fileContainer',
    mode: 'file',
    fileIconSize: 48,
    showFileInfo: true,
    searchable: true,
    showPagination: true
});

fileManager.table();
*/