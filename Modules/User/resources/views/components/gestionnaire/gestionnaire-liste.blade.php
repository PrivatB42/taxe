@php
$config = [
'tableId' => 'table-id',
'ajaxUrl' => route('gestionnaires.data'),
'lengthChange' => true,
'pageLength' => 10,
'buttons' => [],
];

$columns = [

[
'title' => 'Gestionnaire',
'data' => 'personne',
'render' => 'function(data, type, row, meta) {
    if (!data) return "";
    const statusBadge = row.is_active 
        ? `<span class="badge bg-success-subtle text-success ms-2" style="font-size: 0.65rem;">Actif</span>`
        : `<span class="badge bg-danger-subtle text-danger ms-2" style="font-size: 0.65rem;">Inactif</span>`;
    return `
        <div class="d-flex align-items-center">
            <img src="${row.photo}" class="avatar-circle me-3" alt="${data.nom_complet}" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 3px solid ${row.is_active ? "#c8e6c9" : "#f8d7da"};">
            <div>
                <div class="fw-semibold text-dark">${data.nom_complet}${statusBadge}</div>
                <small class="text-muted"><i class="fas fa-envelope me-1"></i>${row.email || "Non renseigné"}</small>
            </div>
        </div>
    `;
}'
],

[
'title' => 'Contact',
'data' => 'telephone',
'render' => 'function(data, type, row, meta) {
    return `
        <div>
            <div class="fw-medium"><i class="fas fa-phone text-primary me-2"></i>${data}</div>
            <small class="text-muted">${row.derniere_connexion ? `<i class="fas fa-clock me-1"></i>` + row.derniere_connexion : "Jamais connecté"}</small>
        </div>
    `;
}'
],

[
'title' => 'Activité',
'data' => 'id',
'render' => 'function(data, type, row, meta) {
    const isOnline = row.is_online;
    const statusDot = isOnline 
        ? `<span class="d-inline-block rounded-circle bg-success me-2" style="width: 8px; height: 8px;"></span>En ligne`
        : `<span class="d-inline-block rounded-circle bg-secondary me-2" style="width: 8px; height: 8px;"></span>Hors ligne`;
    return `
        <div>
            <div class="small">${statusDot}</div>
            <small class="text-muted">${row.actions_aujourdhui || 0} actions aujourd&apos;hui</small>
        </div>
    `;
}',
'className' => 'text-center',
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
            $(row).css("opacity", "0.7");
            }';

@endphp

<x-generic.xtable :config="$config" :columns="$columns" />


<script>
    var tableName = 'table-id';
    var form = 'form-id';
    var btnForm = 'btn-form-id';
    var routeStore = "{{ route('gestionnaires.store') }}";
    var routeUpdate = "{{ route('gestionnaires.update', ':id') }}";
    var routeToggle = "{{ route('gestionnaires.toggle-active', ':id') }}";
    var titleForm = '<i class="fas fa-user-plus me-2 text-primary"></i>Ajouter un gestionnaire';
    var titleUpdate = '<i class="fas fa-user-edit me-2 text-warning"></i>Modifier le gestionnaire';
    var inputsId = ['nom_complet', 'telephone', 'email'];

    function arrayButtons(data, type, row, meta) {
        const editBtn = `
            <button onClick="editForm(${meta.row})" 
               class="btn btn-sm btn-outline-primary" 
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

        const viewBtn = `
            <button onClick="viewActivities(${row.id})" 
               class="btn btn-sm btn-outline-info" 
               style="border-radius: 8px; width: 34px; height: 34px; padding: 0;"
               title="Voir les activités">
               <i class="fas fa-history"></i>
            </button>`;

        return `<div class="d-flex gap-1 justify-content-center">${editBtn}${viewBtn}${toggleBtn}</div>`;
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
        if (typeof updateStats === 'function') {
            setTimeout(updateStats, 500);
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
        document.querySelector('.form-card, .form-card-contribuable')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function viewActivities(id) {
        // Rediriger vers la page des activités avec filtre
        window.location.href = "{{ route('activites-log.index') }}?gestionnaire_id=" + id;
    }

    function toggle(id, is_active) {
        const table = x_datatable(tableName);
        const configModal = configModalChangeStatut(
            is_active 
                ? '<i class="fas fa-exclamation-triangle text-warning me-2"></i>Voulez-vous vraiment désactiver ce gestionnaire ?' 
                : '<i class="fas fa-check-circle text-success me-2"></i>Voulez-vous vraiment activer ce gestionnaire ?',
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