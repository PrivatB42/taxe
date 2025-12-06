@php

$form = xFormBuilder();

$form->action(route('activites.store'))
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