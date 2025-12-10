@php
$config = [
'tableId' => 'table-id',
'ajaxUrl' => route('contribuables-taxes.data', [
'contribuable_id' => $contribuable_id ?? null,
'activite_id' => $activite_id ?? null
]),
'lengthChange' => false,
'buttons' => [],
];

$columns = [

[
'title' => 'Taxe',
'data' => 'taxe.nom',
],

[
'title' => 'Montant',
'data' => 'montant',
'render' => 'function(data, type, row, meta) {
    return data ? x_number_format(data, "XOF") : 0;
}'
],

[
'title' => 'Montant a payer',
'data' => 'montant_a_payer',
'render' => 'function(data, type, row, meta) {
    return data ? x_number_format(data, "XOF") : 0;
}'
],

[
'title' => 'Montant payer',
'data' => 'montant_paye',
'render' => 'function(data, type, row, meta) {
    return data ? x_number_format(data, "XOF") : 0;
}'
],

[
'title' => 'Montant Restant',
'data' => 'montant_a_payer',
'render' => 'function(data, type, row, meta) {
    return data ? x_number_format((data - row.montant_paye), "XOF") : 0;
}'
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

    function arrayButtons(data, type, row, meta) {
        return `

                <button onClick="editForm(${meta.row})" 
                   class="btn btn-sm btn-primary btn-sm">
                   <i class="fas fa-money-bills"></i>
                </button>

            ` ;
    }

    // document.addEventListener('DOMContentLoaded', function() {

    //     x_form_fetch(form, btnForm, {
    //         successCallback: 'refreshTable',
    //         formResetCallback: 'resetForm'
    //     });
    // });

    // function refreshTable() {
    //     x_datatable(tableName).refreshTable()
    //     x_inner('x-alerts-container', '');
    // }

    // function resetForm() {
    //     x_reset_form(form, {
    //         form_action: routeStore,
    //         form_method: 'POST'
    //     });
    //     x_inner('card-title', titleForm);
    // }


    // function editForm($index) {
    //     const table = x_datatable(tableName);
    //     const data = table.getRowData($index);

    //     x_form_edit(
    //         form,
    //         inputsId,
    //         data, {
    //             form_action: routeUpdate.replace(':id', data.id),
    //             form_method: 'POST',
    //         },
    //     );

    //     x_inner('card-title', titleUpdate);
    // }

    // function goto(route) {
    //     window.location.href = route;
    // }


    // function toggle(id, is_active) {
    //     const table = x_datatable(tableName);
    //     const configModal = configModalChangeStatut(
    //         is_active ? 'Voulez-vous vraiment desactiver ?' : 'Voulez-vous vraiment activer ?',
    //         id,
    //         function(config) {
    //             config.buttonAction.color = is_active ? 'danger' : 'success';
    //             config.buttonAction.text = is_active ? 'Desactiver' : 'Activer';
    //         }
    //     );

    //     const action = (id) => {
    //         const url = routeToggle.replace(':id', id);
    //         const callBacks = {
    //             success: function(result, response) {
    //                 x_successNotification(result.message);
    //                 table.refreshTable();
    //             },
    //             error: function(error) {
    //                 x_errorAlert(error.message);
    //             }
    //         };
    //         x_fetch(url, optionsPost(), null, callBacks);
    //     }

    //     confirmModal(configModal, action);
    // }

</script> 