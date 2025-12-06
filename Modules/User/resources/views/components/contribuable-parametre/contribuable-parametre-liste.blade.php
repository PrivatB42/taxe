@php
$config = [
'tableId' => 'table-id-1',
'ajaxUrl' => route('contribuables-parametres.data').'?contribuable_id='.$contribuable->id,
'lengthChange' => false,
'buttons' => [],
];

$columns = [

[
'title' => 'Paramètre',
'data' => 'nom',
'render' => 'function(data, type, row, meta) {
return `${data} : ${row.type}`
}',
],

[
'title' => 'Valeur',
'data' => 'valeur',
],

[
'data' => 'id',
'title' => 'Actions',
'render' => 'function(data, type, row, meta) {
return arrayButtons_(data, type, row, meta)
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
    var tableName_ = 'table-id-1';
    var form_ = 'form-id-1';
    var btnForm_ = 'btn-form-id-1';
    var routeStore_ = "{{ route('contribuables-parametres.store') }}";
    var routeUpdate_ = "{{ route('contribuables-parametres.update', ':id') }}";
    var routeToggle_ = "{{ route('contribuables-parametres.toggle-active', ':id') }}";
    var titleForm_ = 'Ajouter';
    var titleUpdate_ = 'Modifier';
    var inputsId_ = ['nom', 'type', 'valeur'];

    function arrayButtons_(data, type, row, meta) {
        return `
                <a href="#" onClick="editForm_(${meta.row})" 
                   class="btn btn-sm btn-secondary btn-sm">
                   <i class="fas fa-edit"></i>
                </a>

                <button onClick="toggle_(${row.id}, ${row.is_active})" 
                    class="btn btn-sm btn-${row.is_active ? 'danger' : 'success'}">
                    <i class="fas fa-toggle-${row.is_active ? 'off' : 'on'}"></i>
                </button>

            ` ;
    }

    document.addEventListener('DOMContentLoaded', function() {

        x_form_fetch(form_, btnForm_, {
            successCallback: 'refreshTable_',
            formResetCallback: 'resetForm_'
        });
    });

    function refreshTable_() {
        x_datatable(tableName_).refreshTable()
        x_inner('x-alerts-container', '');
    }

    function resetForm_() {
        x_reset_form(form_, {
            form_action: routeStore_,
            form_method: 'POST'
        });
        x_inner('card-title', titleForm_);
    }


    function editForm_($index) {
        const table = x_datatable(tableName_);
        const data = table.getRowData($index);

        x_form_edit(
            form_,
            inputsId_,
            data, {
                form_action: routeUpdate.replace(':id', data.id),
                form_method: 'POST',
            },
        );

        x_inner('card-title', titleUpdate_);
    }


    function toggle_(id, is_active) {
        const table = x_datatable(tableName_);
        const configModal = configModalChangeStatut(
            is_active ? 'Voulez-vous vraiment desactiver ?' : 'Voulez-vous vraiment activer ?',
            id,
            function(config) {
                config.buttonAction.color = is_active ? 'danger' : 'success';
                config.buttonAction.text = is_active ? 'Desactiver' : 'Activer';
            }
        );

        const action = (id) => {
            const url = routeToggle_.replace(':id', id);
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