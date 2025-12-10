@php
$widgets = [
//'topRight' => button_modal('Ajouter', 'btn btn-sm btn-primary', 'fas fa-plus', 'form-modal'),
'topRight' => '<button class="btn btn-sm btn-primary"  onClick="toggle(`create`)"> <i class="fas fa-plus"></i> Ajouter </button>'
];
$config = [
'tableId' => 'table-id',
'ajaxUrl' => route('caisses.data'),
'lengthChange' => true,
'buttons' => [],
'customLayout' => [
'topLeft' => ['length'],
'topRight' => ['widget'],
'topCenter' => ['search'],
'bottomLeft' => ['info'],
'bottomCenter' => [],
'bottomRight' => ['pagination']
],
'widgets' => $widgets,
];

$columns = [
[
'title' => 'Caisse',
'data' => 'nom',
],

[
'title' => 'Statut',
'data' => 'statut',
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
    var tableName = 'table-id';
    var routeStore = "{{ route('caisses.store') }}";
    var routeToggle = "{{ route('caisses.toggle-active', ':id') }}";

    function arrayButtons(data, type, row, meta) {
        return `

                 <button onClick="toggle('toggle', ${row.id}, ${row.is_active})" 
                    class="btn btn-sm btn-${row.is_active ? 'danger' : 'success'}">
                    <i class="fas fa-toggle-${row.is_active ? 'off' : 'on'}"></i>
                </button>

            `;
    }


    function toggle(actionName, id = null, is_active = null) {
        const table = x_datatable(tableName);
        const actions = {
            create : {
                text: 'Voulez-vous vraiment créer une caisse ?',
                route: routeStore,
                colorButton: 'success',
                colorText: 'Créer'
            },
            toggle: {
                text: is_active ? 'Voulez-vous vraiment desactiver ?' : 'Voulez-vous vraiment activer ?',
                route: routeToggle.replace(':id', id),
                colorButton: is_active ? 'danger' : 'success',
                colorText: is_active ? 'Desactiver' : 'Activer'
            }
        };
        const action = actions[actionName]
        const configModal = configModalChangeStatut(
            action.text,
            id,
            function(config) {
                config.buttonAction.color = action.colorButton;
                config.buttonAction.text = action.colorText;
            }
        );

        const x_action = (id) => {
            const url = action.route;
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

        confirmModal(configModal, x_action);
    }
</script>