/**
 * Classe universelle pour gérer les galeries d'images et les tableaux
 * Utilise Bootstrap pour le style
 */
class XGtFile {
    constructor(data, mapping, endpoints = {}, options = {}) {
        this.data = Array.isArray(data) ? data : Object.values(data);
        this.mapping = mapping;
        this.endpoints = endpoints;
        this.options = {
            container: options.container || 'body',
            beforeCall: options.beforeCall || null,
            afterCall: options.afterCall || null,
            confirmMessage: options.confirmMessage || 'Êtes-vous sûr de vouloir effectuer cette action ?',
            imageWidth: options.imageWidth || null,
            imageHeight: options.imageHeight || 200,
            aspectRatio: options.aspectRatio || null,
            showPagination: options.showPagination || false,
            itemsPerPage: options.itemsPerPage || 10,
            searchable: options.searchable || false,
            ...options
        };
        
        this.currentPage = 1;
        this.filteredData = [...this.data];
        this.container = null;
        this.type = null; // 'gallery' ou 'table'
        
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
     * Créer une galerie d'images
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
            card.className = 'card gallery-item';
            card.dataset.id = item[this.mapping.id];
            
            const imageUrl = item[this.mapping.url];
            const imageStyle = this.getImageStyle();
            
            card.innerHTML = `
                <div class="position-relative">
                    <img src="${imageUrl}" 
                         class="card-img-top gallery-image" 
                         style="${imageStyle}"
                         alt="Image ${item[this.mapping.id]}"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlIG5vbiBkaXNwb25pYmxlPC90ZXh0Pjwvc3ZnPg=='">
                    ${this.createActionButtons(item)}
                </div>
            `;
            
            col.appendChild(card);
            gallery.appendChild(col);
        });
        
        return gallery;
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
                th.textContent = key.charAt(0).toUpperCase() + key.slice(1);
                headerRow.appendChild(th);
            }
        });
        
        // Colonne actions si nécessaire
        if (this.hasActions()) {
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
                        td.innerHTML = `<img src="${value}" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">`;
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
        
        // Bouton de téléchargement
        if (this.endpoints.download) {
            const downloadBtn = document.createElement('button');
            downloadBtn.className = 'btn btn-success btn-sm';
            downloadBtn.innerHTML = '<i class="fas fa-download"></i>';
            downloadBtn.title = 'Télécharger';
            downloadBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.downloadItem(item[this.mapping.id]);
            });
            buttonContainer.appendChild(downloadBtn);
        }
        
        // Bouton d'impression
        if (this.endpoints.print) {
            const printBtn = document.createElement('button');
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
            deleteBtn.className = 'btn btn-danger btn-sm';
            deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
            deleteBtn.title = 'Supprimer';
            deleteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.showDeleteModal(item[this.mapping.id]);
            });
            buttonContainer.appendChild(deleteBtn);
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

    hasActions() {
        return this.endpoints.delete || this.endpoints.download || this.endpoints.print;
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
    async downloadItem(itemId) {
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
                a.download = `file_${itemId}`;
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

// Exemple d'utilisation :
/*
const data = [
    { id: 1, name: 'Image 1', url: 'https://picsum.photos/300/200?random=1' },
    { id: 2, name: 'Image 2', url: 'https://picsum.photos/300/200?random=2' }
];

const mapping = {
    id: 'id',
    name: 'name',
    url: 'url'
};

const endpoints = {
    delete: 'https://api.example.com/images/{id}',
    download: (id) => `https://api.example.com/download/${id}`,
    print: 'https://api.example.com/print/{id}'
};

const options = {
    container: '#container',
    imageHeight: 250,
    aspectRatio: '16/9',
    showPagination: true,
    searchable: true,
    beforeCall: (action, id) => console.log(`${action} pour ${id}`),
    afterCall: (action, id, success) => console.log(`${action} ${success ? 'réussi' : 'échoué'}`)
};

// Utilisation normale
const manager = new XGtFile(data, mapping, endpoints, options);
manager.gallery(); // ou manager.table()

// Utilisation avec alias
const x = new x_gt_(data, mapping, endpoints, options);
x.g(); // galerie
x.t(); // tableau
*/