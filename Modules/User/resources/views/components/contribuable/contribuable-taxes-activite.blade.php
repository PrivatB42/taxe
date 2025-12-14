@php
$form = xForm();
$annee = (float)date('Y');
$vars = ['annee' => $annee];
@endphp

@if(session('user.role') != _constantes()::ROLE_CAISSIER)

<table class="table table-stripped">
    <thead>
        <tr>
            <th>Taxes</th>
            <th>Montant</th>
            <th>Montant à payer</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($taxes as $key => $taxe)
        <tr>
            <td>{{ $taxe->nom }}</td>
            <td>
                @if ($taxe->formule)
                <input type="hidden" class="montant-input" data-index="{{ $taxe->id }}" value="{{ calculeTaxeVariable($contribuable, $taxe, $contribuableActivite->id, $vars) }}">
                {{ calculeTaxeVariable($contribuable, $taxe, $contribuableActivite->id, $vars) }}
                @else

                <input type="number"
                    class="form-control form-control montant-input"
                    placeholder="Montant"
                    data-index="{{ $taxe->id }}"
                    onchange="calculeTotal(this.value, '{{ $taxe->id }}', '{{ $taxe->multiplicateur }}')">

                @endif
            </td>
            <td>
                @if ($taxe->formule)
                {{ calculeTaxeVariable($contribuable, $taxe,$contribuableActivite->id, $vars) * $taxe->multiplicateur }}
                @else
                <span id="total-{{ $taxe->id }}"></span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<button id="btn-save" class="btn btn-primary mt-2">Enregistrer</button>

<hr class="mb-3 mt-3">

@endif

<!-- <table class="table table-stripped">
    <thead>
        <tr>
            <th>Taxes</th>
            <th>Montant</th>
            <th>Montant à payer</th>
            <th>Montant payer</th>
            <th>Montant restant</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($contribuableTaxes as $contribuableTaxe)
            <tr>
                <td>{{ $contribuableTaxe->taxe?->nom }}</td>
                <td>{{ $contribuableTaxe->montant }}</td>
                <td>{{ $contribuableTaxe->montant_a_payer }}</td>
                <td>{{ $contribuableTaxe->montant_payer ?? 00 }}</td>
                <td>{{ $contribuableTaxe->montant_a_payer - $contribuableTaxe->montant_payer }}</td>
                <td>{{ $contribuableTaxe->statut }}</td>
            </tr>
        @endforeach
    </tbody>
</table> -->

@include('user::components.contribuable.contribuable-taxes', [
'contribuable_id' => $contribuable->id,
'activite_id' => $contribuableActivite->activite_id
])


<script>
    const role = "{{ session('user.role', '') }}";

    if(role != '{{ _constantes()::ROLE_CAISSIER }}') {

    function calculeTotal(montant, id, multiplicateur) {

        var total = parseFloat(montant) * parseFloat(multiplicateur);
        document.getElementById('total-' + id).innerHTML = total || 0;

    }



    document.getElementById('btn-save').addEventListener('click', function() {

        let inputs = document.querySelectorAll('.montant-input');
        let taxes = [];
        let erreur = false;
        let message = "";

        inputs.forEach(input => {
            let taxe_id = input.dataset.index;
            let montant = parseFloat(input.value);

            // Vérifier si montant est vide, NaN, <= 0
            if (!montant || montant <= 0) {
                erreur = true;
                message = "Veuillez renseigner un montant valide (> 0) pour toutes les taxes.";
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }

            taxes.push({
                taxe_id: taxe_id,
                montant: montant || 0
            });
        });

        if (erreur) {
            alert(message); // tu peux remplacer par un toast ou autre
            return; // on stoppe tout
        }

        console.log("DATA envoyée :", taxes);

        fetch("{{ route('contribuables-taxes.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    taxes: taxes,
                    activite_id: '{{ $contribuableActivite->activite_id }}',
                    contribuable_id: '{{ $contribuableActivite->contribuable_id }}'
                })
            })
            .then(res => res.json())
            .then(data => {
                console.log("Réponse API :", data);
                alert("Enregistré avec succès !");
            })
            .catch(err => {
                console.error(err);
            });

    });

    }
</script>