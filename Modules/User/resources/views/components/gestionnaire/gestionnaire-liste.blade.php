@php
$config = [
'tableId' => 'table-id',
'ajaxUrl' => route('gestionnaires.data'),
'lengthChange' => false,
'buttons' => [],
];

$columns = [

[
'title' => 'Utilisateur',
'data' => 'personne',
'render' => 'function(data, type, row, meta) {
    return data ? image_profile(row.photo, data.nom_complet) : "";
}'
],

[
'title' => 'Contact',
'data' => 'id',
'render' => 'function(data, type, row, meta) {
    return `
    <div>
        <i class="fas fa-phone text-muted me-1"></i>
        <span>${row.telephone}</span>
    </div>
    <div class="mt-1">
        <i class="fas fa-envelope text-muted me-1"></i>
        <small class="text-muted">${row.email || "Non renseigné"}</small>
    </div>
    `;
}'
],

[
'title' => 'Rôle',
'data' => 'role',
'render' => 'function(data, type, row, meta) {
    const roleNames = {
        "admin": "Admin",
        "regisseur": "Régisseur",
        "agent_de_la_regie": "Agent de la Régie",
        "caissier": "Caissier",
        "superviseur": "Superviseur"
    };
    const roleClasses = {
        "admin": "admin",
        "regisseur": "regisseur",
        "agent_de_la_regie": "agent",
        "caissier": "caissier",
        "superviseur": "superviseur"
    };
    const roleName = roleNames[data] || data;
    const roleClass = roleClasses[data] || "admin";
    return `<span class="role-badge ${roleClass}">${roleName}</span>`;
}'
],

[
'title' => 'Statut',
'data' => 'is_active',
'render' => 'function(data, type, row, meta) {
    return data 
        ? `<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Actif</span>`
        : `<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Inactif</span>`;
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
            $(row).addClass("table-danger");
            }';

@endphp

<style>
    .users-list-card {
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }

    .users-list-header {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
        color: white;
        padding: 1.5rem;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        color: #495057;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
    }

    .btn-sm {
        border-radius: 6px;
        padding: 0.4rem 0.8rem;
        transition: all 0.2s ease;
    }

    .btn-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
</style>

<div class="card users-list-card">
    <div class="users-list-header">
        <i class="fas fa-users"></i>
        Liste des Utilisateurs
    </div>
    <div class="card-body p-0">
        <x-generic.xtable :config="$config" :columns="$columns" />
    </div>
</div>


 <script>
    var tableName = 'table-id';
    var form = 'form-id';
    var btnForm = 'btn-form-id';
    var routeStore = "{{ route('gestionnaires.store') }}";
    var routeUpdate = "{{ route('gestionnaires.update', ':id') }}";
    var routeToggle = "{{ route('gestionnaires.toggle-active', ':id') }}";
    var titleForm = 'Ajouter';
    var titleUpdate = 'Modifier';
    var inputsId = ['nom_complet', 'telephone', 'email'];

    function arrayButtons(data, type, row, meta) {
        return `
                <a href="#" onClick="editForm(${meta.row})" 
                   class="btn btn-sm btn-secondary btn-sm">
                   <i class="fas fa-edit"></i>
                </a>

                <button onClick="toggle(${row.id}, ${row.is_active})" 
                    class="btn btn-sm btn-${row.is_active ? 'danger' : 'success'}">
                    <i class="fas fa-toggle-${row.is_active ? 'off' : 'on'}"></i>
                </button>

            ` ;
    }

    document.addEventListener('DOMContentLoaded', function() {

        x_form_fetch(form, btnForm, {
            successCallback: 'refreshTable',
            formResetCallback: 'resetForm'
        });
    });

    function refreshTable() {
        x_datatable(tableName).refreshTable()
        x_inner('x-alerts-container', '');
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
    }


    function toggle(id, is_active) {
        const table = x_datatable(tableName);
        const configModal = configModalChangeStatut(
            is_active ? 'Voulez-vous vraiment desactiver ?' : 'Voulez-vous vraiment activer ?',
            id,
            function(config) {
                config.buttonAction.color = is_active ? 'danger' : 'success';
                config.buttonAction.text = is_active ? 'Desactiver' : 'Activer';
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