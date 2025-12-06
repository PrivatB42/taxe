@php
$form = xForm();
@endphp

<x-generic.card>
    <x-slot name="header">
        <i class="fas fa-user"></i>
        <span id="card-title">{{ ucfirst($contribuable->personne?->nom_complet) }}</span> |
        <span>{{ $contribuable->matricule }}</span>

        <div class="float-end">
            <a href="{{ route('contribuables.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Retour </a>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-md-4">
            @include('user::components.contribuable-activite.contribuable-activite-form',
            [
            'contribuable' => $contribuable,
            ])
        </div>
        <div class="col-md-8">
            @include('user::components.contribuable-activite.contribuable-activite-liste')
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-4">
            @include('user::components.contribuable-parametre.contribuable-parametre-form',
            [
            'contribuable' => $contribuable,
            ])
        </div>
        <div class="col-md-8">
            @include('user::components.contribuable-parametre.contribuable-parametre-liste')
        </div>
    </div>

</x-generic.card>