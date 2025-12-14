@php
$widgets = [
//'topRight' => button_modal('Ajouter', 'btn btn-sm btn-primary', 'fas fa-plus', 'form-modal'),
'topRight' => '<button class="btn btn-sm btn-primary" onClick="toggle(`create`)"> <i class="fas fa-plus"></i> Ajouter </button>'
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
'title' => 'Caissier',
'data' => 'gestionnaire',
'render' => 'function(data, type, row, meta) {
return `
${data?.[0]?.nom_complet || ""}
<input type="text" class="gestionnaire-select" data-caisse_id="${row.id}">
`;
}'
],

[
'title' => 'Montant Encaisser',
'data' => 'id',
'render' => 'function(data, type, row, meta) {
return `
<span class="montant-encaisser" data-caisse_id="${row.id}"> 0 FCFA</span>
`;
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

<style>
    .caisse-hero {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border-radius: 16px;
        padding: 1.5rem;
        color: #0b2c4d;
        box-shadow: 0 10px 30px rgba(0, 176, 255, 0.25);
        margin-bottom: 1.5rem;
    }
    .caisse-hero h3 {
        font-weight: 800;
        margin: 0;
    }
    .caisse-hero p {
        margin: 0.25rem 0 0;
        opacity: 0.85;
    }
</style>

<div class="caisse-hero d-flex align-items-center justify-content-between">
    <div>
        <h3><i class="fas fa-cash-register me-2"></i>Gestion des caisses</h3>
        <p>Assignez un caissier et activez/désactivez les caisses en un clic.</p>
    </div>
    <div>
        {!! $widgets['topRight'] !!}
    </div>
</div>

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


    function toggle(actionName, id = null, is_active = null, params = {}) {
        

        const table = x_datatable(tableName);
        const actions = {
            create: {
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
            },
            associer: {
                text: 'Voulez-vous vraiment associer cette caisse a cet gestionnaire ?',
                route: "{{ route('caisses.associate-gestionnaire', ['caisse_id' => ':caisse_id', 'gestionnaire_id' => ':gestionnaire_id']) }}".replace(':caisse_id', params.caisse_id).replace(':gestionnaire_id', params.gestionnaire_id),
                colorButton: 'success',
                colorText: 'Associer'
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

    function initGestionnaireSelect(el) {
        if (el.dataset.initialized === "1") return; // éviter double init
        el.dataset.initialized = "1";
        const table = x_datatable(tableName)

        const route = '{{ route("gestionnaires.search") }}?is_active=true&role={{ _constantes()::ROLE_CAISSIER }}';
        const gestionnaireSelect = x_select_ajax(el, {
            url: route,
            valueField: 'id',
            labelField: 'nom_complet',
            searchField: ['nom_complet'],
            preload: true,
            plugins: ['remove_button'],
            maxItems: 1,
            onChange: function(id) {
                if (!id) return;
                toggle('associer', null, null, {
                    gestionnaire_id: id,
                    caisse_id: el.dataset.caisse_id
                });
                table.refreshTable();
            }
        });

    }

    // initialisation au chargement complet
    window.onload = function() {
        document.querySelectorAll('.gestionnaire-select').forEach(initGestionnaireSelect);
    };

    // observer pour surveiller les ajouts dynamiques
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // élément
                    if (node.matches('.gestionnaire-select')) {
                        initGestionnaireSelect(node);
                    }
                    // si le select est dans un container ajouté
                    node.querySelectorAll?.('.gestionnaire-select').forEach(initGestionnaireSelect);
                }
            });
        });
    });

    // lancer la surveillance
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    function sumPaiement(el) {
        el.textContent = 'Calcul...';
        const caisse_id = el.dataset.caisse_id;
        const url = "{{ route('paiements.sum') }}";
        const callBacks = {
            success: function(result, response) {
                el.textContent = result + ' FCFA';
            },
            error: function(error) {
                x_errorAlert(error.message);
            }
        };
        const data = {
            caisse_id: caisse_id,
            date_paiement: "{{ now()->format('Y-m-d') }}"
        };
        x_fetch(url, optionsPost(data), null, callBacks);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Fonction qui déclenche sumPaiement pour chaque élément
        function triggerSumPaiement() {
            document.querySelectorAll('.montant-encaisser').forEach(function(el) {
                sumPaiement(el);
            });
        }

        // Lancer au chargement de la page
        triggerSumPaiement();

        // Lancer toutes les 30 secondes (30000 ms)
        setInterval(triggerSumPaiement, 10000);
    });
</script>