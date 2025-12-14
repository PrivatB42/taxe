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

@include('user::components.contribuable.modal-paiement')


<script>
    var tableName = 'table-id';

    function arrayButtons(data, type, row, meta) {
        return `

                <button onClick="paiementModal(${meta.row})" 
                   class="btn btn-sm btn-primary btn-sm"
                   id="btn-modal-paie"  data-bs-toggle="modal" data-bs-target="#modal-paie"
                   >
                   <i class="fas fa-money-bills"></i>
                </button>

            `;
    }


    function paiementModal($index) {
        const table = x_datatable(tableName);
        const data = table.getRowData($index);
        const multiple = !data?.taxe?.formule ? data.montant : 100;
        const nb = '( Le montant que vous voulez payer doit etre multiple de : ' + x_number_format(multiple, "XOF") + ' )';

        x_inner('taxe_nom', data.taxe.nom);
        x_inner('montant_total', x_number_format(data.montant_a_payer, "XOF"));
        x_inner('montant_restant', x_number_format((data.montant_a_payer - data.montant_paye), "XOF"));
        x_inner('nb', nb);

        x_val('contribuable_taxe_id', data.id);

        const mask_montant_paie = x_mask_currency('#montant_paie_visible');

        const btn = x_('btn-paie');

        mask_montant_paie.on('accept', function(e) {
            x_val('montant_paie', mask_montant_paie.unmaskedValue);
            if (mask_montant_paie.unmaskedValue < Number(multiple)) {
                x_inner('invalid', 'le montant que vous voulez payer doit etre superieur à : ' + x_number_format(multiple, "XOF"));
                btn.setAttribute('disabled', 'disabled');
                x_inner('recap', '');
            } else {
                x_inner('invalid', '');
                const montant_paye = mask_montant_paie.unmaskedValue;
                const montant_paye_text = 'Montant payé : ' + x_number_format(montant_paye, "XOF") + '<br>';
                const montant_preleve = Math.floor(mask_montant_paie.unmaskedValue / multiple) * multiple;
                const montant_preleve_text = 'Montant prelevé : ' + x_number_format(montant_preleve, "XOF") + '<br>';

                const monnaie_text = 'Monnaie : ' + x_number_format(montant_paye - montant_preleve, "XOF");
                x_inner('recap', montant_paye_text + montant_preleve_text + monnaie_text);
                btn.removeAttribute('disabled');
            }
        });

    }

    // document.addEventListener('DOMContentLoaded', function() {


    //     x_form_fetch('form-paie', 'btn-paie', {
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


    function paiement() {
        const table = x_datatable(tableName);
        const configModal = configModalChangeStatut(
            'Voulez-vous vraiment payer ?',
            null,
            function(config) {
                config.buttonAction.color = 'success';
                config.buttonAction.text = 'Payer';
            }
        );

        const data = {
            contribuable_taxe_id: x_val('contribuable_taxe_id'),
            montant_paie: x_val('montant_paie'),
        };

        const fetchPaiement = () => {
            const url = "{{ route('paiements.store') }}";
            const callBacks = {
                success: function(result, response) {
                    x_successNotification(result.message);
                    table.refreshTable();

                    const route = "{{ route('paiements.recu', ':reference') }}".replace(':reference', result.paiement.reference);

                    x_val('montant_paie', '');
                    x_val('montant_paie_visible', '');
                    x_inner('recap', '');
                    x_inner('invalid', '');

                    x_('closeModal').click();

                    setTimeout(() => {
                        window.location.href = route;
                    }, 1000);

                },
                error: function(error) {
                    x_errorAlert(error.message, 5000, 'modal-paie-errors');
                }
            };
            x_fetch(url, optionsPost(data), null, callBacks);
        }

        confirmModal(configModal, fetchPaiement);
    }
</script>