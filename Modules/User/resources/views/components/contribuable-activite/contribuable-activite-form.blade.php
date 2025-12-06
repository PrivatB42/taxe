@php

$form = xFormBuilder();

$form->action(route('contribuables-activites.store'))
->method('POST')
->form(['id' => 'form-id'])
->csrf(true)

->hidden('contribuable_id', $contribuable->id)

->select(
'activite_id',
'Activité',
$form->mapOptions($activites, 'id', 'nom', 'Selectionnez une activité'),
['required' => true]
)

->text(
'annee_debut',
'Annee de debut',
['required' => true, 'value' => date('Y')],
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