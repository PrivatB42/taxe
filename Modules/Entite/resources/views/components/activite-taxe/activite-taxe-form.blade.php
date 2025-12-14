@php

$form = xFormBuilder();

$form->action(route('activites-taxes.store'))
->method('POST')
->form(['id' => 'form-id'])
->csrf(true)

->select(
'activite_id',
'Activite',
$form->mapOptions($activites, 'id', 'nom', 'Selectionnez une activité'),
['required' => true]
)

->select(
'taxe_id',
'Taxe',
$form->mapOptions($taxes, 'id', 'nom', 'Selectionnez une taxe'),
['required' => true]
)

->button('Valider', ['id' => 'btn-form-id'])
->render();
@endphp


<x-generic.card>
    <x-slot name="header">
        <i class="fas fa-building"></i>
        <span id="card-title">Ajouter</span>
        <a href="#" class="btn btn-sm btn-primary float-end" onClick="resetForm()">Renitialiser</a>
    </x-slot>
    {!! $form !!}
</x-generic.card>