@php
$config = [
'tableId' => 'table-id',
'ajaxUrl' => route('paiements.data'),
'lengthChange' => true,
'buttons' => [],
'customLayout' => [
'topLeft' => ['length'],
'topRight' => ['search'],
'topCenter' => [],
'bottomLeft' => ['info'],
'bottomCenter' => [],
'bottomRight' => ['pagination']
],

'lengthMenu' => [10, 25, 50, 100, 200, 300, 400, 500],
'pageLength' => 100

];

$columns = [
[
'title' => 'Caisse',
'data' => 'caisse.nom',
'render' => 'function(data, type, row, meta) {
return `
<span>${data}</span> <br>
<small class="text-muted">${row.caissier?.personne?.nom_complet || ""}</small>
`
}'
],

[
'title' => 'Contribuable',
'data' => 'contribuable.personne.nom_complet',
],

[
'title' => 'Reference',
'data' => 'reference',
],

[
'title' => 'Montant',
'data' => 'montant',
'render' => 'function(data, type, row, meta) {
return x_number_format(data, "XOF")
}'
],

[
'title' => 'Date paiement',
'data' => 'date_paiement',
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


@endphp

<div class="row">

    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="text-muted small text-uppercase mb-3">Total du jour</div>
                        <h3 class="fw-bold mb-0">{{ $totalPaiementToday }} FCFA</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                        <i class="fas fa-money-check text-primary fs-5"></i>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="text-muted small text-uppercase mb-3">Total du jour activé</div>
                        <h3 class="fw-bold mb-0">{{ $totalPaiementTodayActive }} FCFA</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                        <i class="fas fa-money-bill text-primary fs-5"></i>
                    </div>
                </div>

            </div>
        </div>
    </div>


</div>


<x-generic.card>
    <x-generic.xtable :config="$config" :columns="$columns" />
</x-generic.card>


<script>
    var tableName = 'table-id';
    var routeActive = "{{ route('paiements.activer', ':paiement_id') }}";

    function arrayButtons(data, type, row, meta) {
        if (row.date_activement) {
            return '***';
        } else {
            return `

                 <button onClick="choix_action('activer', ${row.id})" 
                    class="btn btn-sm btn-success">
                    <i class="fas fa-toggle-on"></i>
                </button>

            `
        };
    }


    function choix_action(actionName, id = null) {

        const table = x_datatable(tableName);
        const actions = {
            activer: {
                text: 'Voulez-vous vraiment activer cet paiement une fois activer il ne pourra plus etre desactiver ?',
                route: routeActive.replace(':paiement_id', id),
                colorButton: 'success',
                colorText: 'Activer'
            },
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
                    window.location.reload();
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