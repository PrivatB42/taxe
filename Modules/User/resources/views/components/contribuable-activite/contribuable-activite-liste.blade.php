@php
$config = [
'tableId' => 'table-id',
'ajaxUrl' => route('contribuables-activites.data').'?contribuable_id='.$contribuable->id,
'lengthChange' => false,
'buttons' => [],
];

$columns = [

[
'title' => 'Activite',
'data' => 'activite.nom',
],

[
'title' => 'Annee de début',
'data' => 'annee_debut',
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

<x-generic.card>
    <x-generic.xtable :config="$config" :columns="$columns" />
</x-generic.card>


 <script>
    const role = '{{ session("user.role", "") }}'
    var tableName = 'table-id';
    var form = 'form-id';
    var btnForm = 'btn-form-id';
    var routeStore = "{{ route('contribuables-activites.store') }}";
    var routeUpdate = "{{ route('contribuables-activites.update', ':id') }}";
    var routeToggle = "{{ route('contribuables-activites.toggle-active', ':id') }}";
    var titleForm = 'Ajouter';
    var titleUpdate = 'Modifier';
    var inputsId = ['activite_id', 'annee_debut'];

    function arrayButtons(data, type, row, meta) {
        const route = "{{ route('contribuables.show', ['matricule' => $contribuable->matricule, 'action' => ':action', 'contribuable_activite_id' => ':id']) }}".replace(':id', row.id);
        let buttons = `

                <button onClick="goto('${route.replace(':action', 'taxes') }')" 
                   class="btn btn-sm btn-info btn-sm">
                   <i class="fas fa-stamp"></i>
                </button>

            ` ;

            if(role != '{{ _constantes()::ROLE_CAISSIER }}') {
                buttons += `
                <button onClick="goto('${route.replace(':action', 'constantes') }')" 
                   class="btn btn-sm btn-primary btn-sm">
                   <i class="fas fa-c"></i>
                </button>

                <button onClick="editForm(${meta.row})" 
                   class="btn btn-sm btn-secondary btn-sm">
                   <i class="fas fa-edit"></i>
                </button>

                <button onClick="toggle(${row.id}, ${row.is_active})" 
                    class="btn btn-sm btn-${row.is_active ? 'danger' : 'success'}">
                    <i class="fas fa-toggle-${row.is_active ? 'off' : 'on'}"></i>
                </button>
                 `;
            }

            return buttons;
    }

    function goto(ulr) {
        window.location.href = ulr;
    }

    if(role != '{{ _constantes()::ROLE_CAISSIER }}') {

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

}

</script> 