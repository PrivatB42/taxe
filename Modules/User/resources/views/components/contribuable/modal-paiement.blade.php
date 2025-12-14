@php
$form = xForm();
@endphp

<x-generic.c-modal.content-modal modal-id="modal-paie" size="md" modal-title="Paiement">
    <div id="modal-paie-errors" class="mb-3"></div>

    Taxe : <span id="taxe_nom"></span> <br> montant Total à payer : <span id="montant_total"></span> <br> Montant Restant à payer : <span id="montant_restant"></span>

    <p id="nb" class="text-warning mt-2 mb-3"></p>

    <form action="{{ route('paiements.store') }}" method="post" id="form-paie">
        @csrf
        {!! 
        $form::hidden(
            'contribuable_taxe_id',
            ''
        )
         !!}
        {!!
        $form::text(
        'montant_paie_visible',
        'Montant',
         0,
        ['required' => true, 'placeholder' => 'Montant']
        )
        !!}
         <input type="hidden" name="montant_paie" id="montant_paie" value="0">
         <small class="text-danger" id="invalid"></small>
         <div id="recap"></div>
    </form>

    <x-slot name="footer">
        <button type="button" id="closeModal" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" form="paiement-form" class="btn btn-success" id="btn-paie" onclick="paiement()" disabled>
            <i class="fas fa-money-bill"></i> Payer
        </button>
    </x-slot>
</x-generic.c-modal.content-modal>
