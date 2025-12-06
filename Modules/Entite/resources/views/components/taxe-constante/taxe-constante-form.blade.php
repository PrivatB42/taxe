@php

$form = xFormBuilder();

$form->action(route('taxes-constantes.store'))
->method('POST')
->form(['id' => 'form-id'])
->csrf(true)


->select(
'taxe_id',
'Taxe',
$form->mapOptions($taxes, 'id', 'nom', 'Selectionnez une taxe'),
['required' => true]
)

->select(
'type',
'Type de constante',
$form->mapOptions(_constantes()::TYPE_CONSTANTES_TAXE, 'type', 'libelle', 'Selectionnez un type'),
['required' => true]
)

->text(
'nom',
'Nom',
['required' => true, 'placeholder' => 'Entrez le nom']
)

->text(
'valeur',
'Valeur',
['required' => true, 'placeholder' => 'Entrez la valeur']
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