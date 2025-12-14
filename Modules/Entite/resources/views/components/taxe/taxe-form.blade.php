@php

$form = xFormBuilder();

$form->action(route('taxes.store'))
->method('POST')
->form(['id' => 'form-id'])
->csrf(true)


->text('nom',
'Nom',
[
'required' => true,
'placeholder' => 'Entrez le nom',
]
)

->text('code',
'Code',
[
'required' => true,
'placeholder' => 'Entrez le code', 
]
)

->textarea('formule',
'Formule',
[
'placeholder' => 'Entrez la formule', 
]
)

->select(
'multiplicateur',
'Multiplicateur',
$form->mapOptions(_constantes()::MULTIPLICATEUR_TAXE, 'value', 'label', 'Selectionnez un multiplicateur'),
['required' => true],
'',
12
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