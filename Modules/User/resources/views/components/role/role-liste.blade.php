@php
$config = [
'tableId' => 'table-id',
'ajaxUrl' => route('roles.data'),
'lengthChange' => false,
'buttons' => [],
];

$columns = [

[
'title' => 'Code',
'data' => 'code',
'render' => 'function(data, type, row, meta) {
    return `<code class="bg-light px-2 py-1 rounded">${data}</code>`;
}'
],

[
'title' => 'Nom',
'data' => 'nom',
],

[
'title' => 'Description',
'data' => 'description',
'render' => 'function(data, type, row, meta) {
    return data ? `<small class="text-muted">${data}</small>` : "-";
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
    .role-list-card {
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }

    .role-list-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

<div class="card role-list-card">
    <div class="role-list-header">
        <i class="fas fa-list"></i>
        Liste des Rôles
    </div>
    <div class="card-body p-0">
        <x-generic.xtable :config="$config" :columns="$columns" />
    </div>
</div>

<script>
    var tableName = 'table-id';
    var form = 'form-id';
    var btnForm = 'btn-form-id';
    var routeStore = "{{ route('roles.store') }}";
    var routeUpdate = "{{ route('roles.update', ':id') }}";
    var routeToggle = "{{ route('roles.toggle-active', ':id') }}";
    var routeDelete = "{{ route('roles.delete', ':id') }}";
    var titleForm = 'Ajouter un Rôle';
    var titleUpdate = 'Modifier le Rôle';
    var inputsId = ['code', 'nom', 'description'];

    function arrayButtons(data, type, row, meta) {
        let buttons = `
            <a href="#" onClick="editForm(${meta.row})" 
               class="btn btn-sm btn-secondary btn-sm">
               <i class="fas fa-edit"></i>
            </a>

            <button onClick="toggle(${row.id}, ${row.is_active})" 
                class="btn btn-sm btn-${row.is_active ? 'danger' : 'success'}">
                <i class="fas fa-toggle-${row.is_active ? 'off' : 'on'}"></i>
            </button>
        `;

        // Ne pas permettre la suppression du rôle admin
        if (row.code !== 'admin') {
            buttons += `
                <button onClick="deleteRole(${row.id}, '${row.nom}')" 
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i>
                </button>
            `;
        }

        return buttons;
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
            is_active ? 'Voulez-vous vraiment désactiver ce rôle ?' : 'Voulez-vous vraiment activer ce rôle ?',
            id,
            function(config) {
                config.buttonAction.color = is_active ? 'danger' : 'success';
                config.buttonAction.text = is_active ? 'Désactiver' : 'Activer';
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

    function deleteRole(id, nom) {
        const table = x_datatable(tableName);
        const modalConfig = configModal(
            `Voulez-vous vraiment supprimer le rôle "${nom}" ? Cette action est irréversible.`,
            id,
            'center',
            function(config) {
                config.buttonAction.color = 'danger';
                config.buttonAction.text = 'Supprimer';
            }
        );

        const action = (id) => {
            const url = routeDelete.replace(':id', id);
            const options = optionsPost();
            // Forcer la méthode DELETE avec un FormData
            options.method = 'POST';
            if (!options.body) {
                options.body = new FormData();
            }
            options.body.append('_method', 'DELETE');

            const callBacks = {
                success: function(result, response) {
                    x_successNotification(result.message || 'Rôle supprimé avec succès');
                    table.refreshTable();
                },
                error: function(error) {
                    const message = error?.message || 'Suppression impossible (rôle utilisé ou erreur serveur)';
                    x_errorAlert(message, {
                        title: 'Suppression impossible',
                        icon: 'fas fa-exclamation-triangle',
                        type: 'warning'
                    });
                }
            };
            x_fetch(url, options, null, callBacks);
        }

        confirmModal(modalConfig, action);
    }
</script>

