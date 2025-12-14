@php

$caisse = session('user.caisse');
$is_ouvert = $caisse?->statut == _constantes()::STATUT_OUVERT && $caisse?->is_active; 

$action = $is_ouvert ? _constantes()::STATUT_FERMER : _constantes()::STATUT_OUVERT;

@endphp


<x-generic.card>

    @if (!$caisse)

    <div class="text-center">
        <h3 class="text-muted">Désolé vous n'avez pas accès a une caisse</h3>
    </div>

    @else

    <x-slot name="header">
        <i class="fas fa-building"></i>
        <span id="card-title">{{ $caisse?->nom ?? 'Caisse' }}</span>
        <span class="badge {{ $is_ouvert ? 'bg-success' : 'bg-danger' }}"> {{ $is_ouvert ? 'Ouverte' : 'Fermée' }}</span>
    </x-slot>

    <div class="text-center">
        <button class="btn btn-{{ $is_ouvert ? 'danger' : 'success' }} btn-xxl" id="power-caisse">
            <i class="fas fa-power-off "></i>
        </button>
    </div>

    @endif

</x-generic.card>


<script>

    document.getElementById('power-caisse').addEventListener('click', function() {
        ouverture_fermeture('{{ $action }}');
    })

    function ouverture_fermeture(action) {
        const configModal = configModalChangeStatut(
            action == '{{ _constantes()::STATUT_OUVERT }}' ? 'Voulez-vous vraiment ouvrir la caisse ?' : 'Voulez-vous vraiment fermer la caisse ?',
            null,
            function(config) {
                config.buttonAction.color = action == '{{ _constantes()::STATUT_OUVERT }}' ? 'success' : 'danger';
                config.buttonAction.text = action == '{{ _constantes()::STATUT_OUVERT }}' ? 'Ouvrir' : 'Fermer';
            }
        );

        const x_action = () => {
            const url = "{{ route('caisses.ouvrir-fermer', ':action') }}".replace(':action', action);
            const callBacks = {
                success: function(result, response) {
                    x_successNotification(result.message);
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