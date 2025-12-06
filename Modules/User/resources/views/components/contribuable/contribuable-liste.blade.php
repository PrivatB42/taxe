@php
$config = [
'tableId' => 'table-id',
'ajaxUrl' => route('contribuables.data'),
'lengthChange' => true,
'pageLength' => 10,
'buttons' => [],
];

$columns = [

[
'title' => 'Contribuable',
'data' => 'personne',
'render' => 'function(data, type, row, meta) {
    if (!data) return "";
    const statusIndicator = row.is_active 
        ? `<span class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-white" style="width: 12px; height: 12px;"></span>`
        : `<span class="position-absolute bottom-0 end-0 bg-danger rounded-circle border border-white" style="width: 12px; height: 12px;"></span>`;
    return `
        <div class="d-flex align-items-center">
            <div class="position-relative me-3">
                <img src="${row.photo}" class="contribuable-avatar" alt="${data.nom_complet}" 
                     style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 3px solid ${row.is_active ? "#c8e6c9" : "#f8d7da"};">
                ${statusIndicator}
            </div>
            <div>
                <div class="fw-bold text-dark">${data.nom_complet}</div>
                <small class="text-muted"><i class="fas fa-phone me-1"></i>${data.telephone || "Non renseigné"}</small>
            </div>
        </div>
    `;
}'
],

[
'title' => 'Matricule',
'data' => 'matricule',
'render' => 'function(data) {
    return `<span class="matricule-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; font-family: monospace;">${data}</span>`;
}'
],

[
'title' => 'Adresse',
'data' => 'adresse_complete',
'render' => 'function(data, type, row, meta) {
    return `
        <div>
            <div class="text-dark"><i class="fas fa-map-marker-alt text-danger me-2"></i>${data || "Non renseignée"}</div>
        </div>
    `;
}'
],

[
'data' => 'id',
'title' => 'Actions',
'render' => 'function(data, type, row, meta) {
return arrayButtons(data, type, row, meta)
}',
'searchable' => false,
'orderable' => false,
'className' => 'text-center',
]

];

$config['rowCallback'] = 'if (!data.is_active) {
            $(row).css("opacity", "0.6");
            }';

@endphp

<x-generic.xtable :config="$config" :columns="$columns" />


<script>
    var tableName = 'table-id';
    var form = 'form-id';
    var btnForm = 'btn-form-id';
    var routeStore = "{{ route('contribuables.store') }}";
    var routeUpdate = "{{ route('contribuables.update', ':id') }}";
    var routeToggle = "{{ route('contribuables.toggle-active', ':id') }}";
    var routeShow = "{{ route('contribuables.show', ':matricule') }}"
    var titleForm = '<i class="fas fa-user-plus me-2"></i>Ajouter un contribuable';
    var titleUpdate = '<i class="fas fa-user-edit me-2 text-warning"></i>Modifier le contribuable';
    var inputsId = ['nom_complet', 'telephone', 'adresse_complete'];

    function arrayButtons(data, type, row, meta) {
        const route = routeShow.replace(':matricule', row.matricule);
        
        const viewBtn = `
            <button onClick="goto('${route}')" 
               class="btn btn-sm btn-primary" 
               style="border-radius: 8px; width: 34px; height: 34px; padding: 0;"
               title="Voir détails">
               <i class="fas fa-eye"></i>
            </button>`;

        const editBtn = `
            <button onClick="editForm(${meta.row})" 
               class="btn btn-sm btn-outline-secondary" 
               style="border-radius: 8px; width: 34px; height: 34px; padding: 0;"
               title="Modifier">
               <i class="fas fa-edit"></i>
            </button>`;

        const toggleBtn = `
            <button onClick="toggle(${row.id}, ${row.is_active})" 
                class="btn btn-sm btn-${row.is_active ? 'outline-danger' : 'outline-success'}"
                style="border-radius: 8px; width: 34px; height: 34px; padding: 0;"
                title="${row.is_active ? 'Désactiver' : 'Activer'}">
                <i class="fas fa-${row.is_active ? 'ban' : 'check-circle'}"></i>
            </button>`;

        return `<div class="d-flex gap-1 justify-content-center">${viewBtn}${editBtn}${toggleBtn}</div>`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        x_form_fetch(form, btnForm, {
            successCallback: 'refreshTable',
            formResetCallback: 'resetForm'
        });
    });

    function refreshTable() {
        x_datatable(tableName).refreshTable();
        x_inner('x-alerts-container', '');
        // Mettre à jour les stats
        if (typeof updateContribuableStats === 'function') {
            setTimeout(updateContribuableStats, 500);
        }
    }

    function resetForm() {
        x_reset_form(form, {
            form_action: routeStore,
            form_method: 'POST'
        });
        x_inner('card-title', titleForm);
    }

    function editForm($index) {
        const table = x_datatable(tableName);
        const data = table.getRowData($index);

        x_form_edit(
            form,
            inputsId,
            data, {
                form_action: routeUpdate.replace(':id', data.id),
                form_method: 'POST',
            },
        );

        x_inner('card-title', titleUpdate);
        
        // Scroll vers le formulaire
        document.querySelector('.form-card-contribuable')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function goto(route) {
        window.location.href = route;
    }

    function toggle(id, is_active) {
        const table = x_datatable(tableName);
        const configModal = configModalChangeStatut(
            is_active 
                ? '<i class="fas fa-exclamation-triangle text-warning me-2"></i>Voulez-vous vraiment désactiver ce contribuable ?' 
                : '<i class="fas fa-check-circle text-success me-2"></i>Voulez-vous vraiment activer ce contribuable ?',
            id,
            function(config) {
                config.buttonAction.color = is_active ? 'danger' : 'success';
                config.buttonAction.text = is_active ? '<i class="fas fa-ban me-2"></i>Désactiver' : '<i class="fas fa-check me-2"></i>Activer';
            }
        );

        const action = (id) => {
            const url = routeToggle.replace(':id', id);
            const callBacks = {
                success: function(result, response) {
                    x_successNotification(result.message);
                    table.refreshTable();
                },
                error: function(error) {
                    x_errorAlert(error.message);
                }
            };
            x_fetch(url, optionsPost(), null, callBacks);
        }

        confirmModal(configModal, action);
    }
</script> 