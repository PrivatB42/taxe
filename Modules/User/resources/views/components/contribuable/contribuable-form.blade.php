@php

$form = xFormBuilder();

$form->action(route('contribuables.store'))
->method('POST')
->form(['id' => 'form-id'])
->csrf(true)


->text('nom_complet',
'Nom complet',
[
'required' => true,
'placeholder' => 'Entrez le nom complet',
]
)

->number('telephone',
'Telephone',
[
'required' => true,
'placeholder' => 'Entrez le numéro de téléphone',
]
)

->textarea('adresse_complete',
'Adresse complète',
[ 
'placeholder' => 'Entrez l\'adresse complète',
]
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